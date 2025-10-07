<?php

namespace App\Http\Controllers\Api\user;

use App\Helpers\ApiResponse;
use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventMainResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventInvitation;
use App\Models\EventItem;
use App\Models\EventTask;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function store(EventRequest $request)
    {
        $validated_data = $request->validated();

        $validated_data['user_id'] = $request->user()->id;

        $validated_data['date'] = Carbon::createFromFormat('Y-m-d g:i A' , $request->date)->format('Y-m-d H:i:s');
        $validated_data['price'] = 0; // default

        $event = Event::create(Arr::except($validated_data, ['items' , 'images']));

        if(!$event)
            return ApiResponse::sendResponse(500 , 'events failed to store' , []);

        if($request->has('items'))
        {
            foreach($request->items  as $item)
            {
                $product = Product::find($item['item_id']);
                $package = Package::find($item['item_id']);

                if($package)
                {
                    $item['type']     = 'package';
                    $item['store_id'] = $package->store_id;
                    $item['price']    = $package->final_price;
                    
                }
                if($product)
                {
                    $item['type'] = 'product';
                    $item['store_id'] = $product->store_id;
                    if(!empty($item['option_id']))
                    {
                        $option = ProductOption::find($item['option_id']);
                        $item['price'] = $option->price;
                    }
                    else{
                        $item['price'] = $product->price;
                    }
                }
                $event->items()->create($item);
            }
        }
        $event->load('items');
        $event->update(['price' => $event->calculatePrice()]);


        if($request->has('images'))
        {
            foreach($request->images as $image)
            {
                $image['image'] = StoreImage::upload($image['image'] , 'events');
                $event->images()->create($image);
            }
        }


        return ApiResponse::sendResponse(201 , 'event stored successfully' , new EventMainResource($event));
    }

    public function updateMainData(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => ['required' , 'string'],
            'date'                  => ['required' , 'date_format:Y-m-d g:i A'],
            'latitude'              => ['required', 'numeric', 'between:-90,90'],
            'longitude'             => ['required', 'numeric', 'between:-180,180'],
            'number_of_guests'      => ['required' , 'integer' , 'min:1'],
            'additional_details'    => ['nullable' , 'string'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $validated_data = $validator->validated();

        $validated_data['user_id'] = $request->user()->id;

        $validated_data['date'] = Carbon::createFromFormat('Y-m-d g:i A' , $request->date)->format('Y-m-d H:i:s');

        $event = Event::find($event_id);

        if(!$event)
            return ApiResponse::sendResponse(404 , 'events not found' , []);

        if($request->user()->id != $event->user_id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        } 
        
        $record = $event->update($validated_data);
        if(!$record)
            return ApiResponse::sendResponse(500 , 'events failed to update' , []);

        return ApiResponse::sendResponse(201, 'event updated successfully', new EventMainResource($event));

    }

    public function show(Request $request , $event_id)
    {
        $event = Event::with(['images' , 'items.product' , 'items.package'])->find($event_id);

        if(!$event)
            return ApiResponse::sendResponse(404 , 'event Not Found' , []);

        return ApiResponse::sendResponse(200 , 'event retrieved successfully' , new EventResource($event));
    }

    public function edit_image(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image' ,'mimes:jpg,jpeg,png'],
            'type' => ['required' , 'in:decore,place,food'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $event = Event::findOrFail($event_id);

        if($event->user_id != $request->user()->id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        }
        $image = $event->images()->create([
            'image' => StoreImage::upload($request->image , 'events'),
            'type'  => $request->type
        ]);

        return ApiResponse::sendResponse(201 , 'image stored successfully' , ['image'=>$image , 'event_id' => $event->id]);
    }

    public function delete_image(Request $request , $image_id)
    {
        $image = EventImage::findOrFail($image_id);
        if($image->event->user_id != $request->user()->id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        }
        $image->delete();
        return ApiResponse::sendResponse(200 , 'image isdeleted successfully' , []);
    }

    public function index_images(Request $request , $event_id)
    {
         $validator = Validator::make($request->all(), [
            'type' => ['in:decore,place,food'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $event = Event::findOrFail($event_id);
        $images = $event->images;

        if($request->has('type'))
        {
            $images = $event->images()->where('type' , $request->type)->get();
        }

        if(count($images)>0)
        {
            return ApiResponse::sendResponse(200 , 'images retrieved succesfully' , $images);
        }
        
        return ApiResponse::sendResponse(200 , 'images is empty' , []);
    }

    public function edit_item(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
        'item_id'   => ['required'],
        'option_id' => ['nullable'],
        'quantity'  => ['required' , 'integer' , 'min:1']
        ], [], []);
        
        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $product = Product::find($request->item_id);
        $package = Package::find($request->item_id);
        if(!$product && !$package)
            return ApiResponse::sendResponse(403 , 'invalid item_id' , []);

        if($package && empty($request->option_id))
        {
            $type = 'package';
            $price = $package->final_price;
            $store_id = $package->store_id;
        }
        if($package && !empty($request->option_id))
        {
           return ApiResponse::sendResponse(403 , 'package doesnt have option ' ,[]);
        }
        if ($product) 
        {
            $options = $product->options;

            if ($options->isNotEmpty() && empty($request['option_id'])) {

                return ApiResponse::sendResponse(422 , 'This product requires an option.' , []);
            } 
            elseif (!empty($item['option'])) {
                if (! ProductOption::where('id', $request['option_id'])->exists()) {
                    return ApiResponse::sendResponse(422 , 'Invalid option ID.' , []);
                } 
                elseif ($options->isEmpty()) {
                    return ApiResponse::sendResponse(422 ,'This product does not contain options.' , []);
                } 
                elseif (!$options->where('id', $request['option_id'])->count()) {
                    return ApiResponse::sendResponse(422 ,'This option does not belong to this product.' , []);
                }
            }

            $option = ProductOption::find($request->option_id);
            $type = 'product';
            $price = $option?$option->price:$product->price;
            $store_id = $product->store_id;
        }

        $event = Event::findOrFail($event_id);

        if($event->user_id != $request->user()->id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);

        }

        $item = $event->items()->create([
            'item_id' => $request->item_id,
            'item_type' => $type,
            'price' => $price,
            'store_id' => $store_id,
            'quantity' => $request->quantity
        ]);

        return ApiResponse::sendResponse(201 , 'item stored successfully' ,['item' => $item , 'event_id' => $event->id] );
    }

    public function delete_item(Request $request , $id)
    {
        $item = EventItem::findOrFail($id);
        if($item->event->user_id != $request->user()->id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        }
        if($item->status != 'pending')
        {     
            return ApiResponse::sendResponse(403 , 'item is not pending' , []);
        }

        $item->delete();
        return ApiResponse::sendResponse(200 , 'item deleted successfully', []);
    }

    public function edit_tasks(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
        'tasks'   => ['required' , 'array'],
        ], [], []);
        
        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $event = Event::findOrFail($event_id);

        foreach($request->tasks as $task)
        {
            $event->tasks()->create(['task' => $task]);
        }

        return ApiResponse::sendResponse(201 , 'tasks stored successfully' , ['tasks' => $event->tasks]);
    }

    public function index_tasks(Request $request , $event_id)
    {
        $event = Event::findOrFail($event_id);

        $tasks = $event->tasks;

        if(count($tasks) > 0)
            return ApiResponse::sendResponse(200 , 'tasks retrieved successfully' , $tasks);
        return ApiResponse::sendResponse(200 , 'tasks is empty' , []);
         
    }

    public function do_task(Request $request , $task_id)
    {
        $task = EventTask::findOrFail($task_id);

        if($request->user()->id != $task->event->user_id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        }

        $task->update(['is_done' => true]);

        $task->makeHidden('event');
        return ApiResponse::sendResponse(201 , 'task done successfully' , $task);
    }

    public function delete_task(Request $request , $task_id)
    {
        $task = EventTask::find($task_id);

        if(!$task)
            return ApiResponse::sendResponse(404 , 'task not found' , []);
        if($request->user()->id != $task->event->user_id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        }
        $task->delete();

        return ApiResponse::sendResponse(200 , 'task delete successfully' , []);
    }

    public function send_invitation(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:users,id' ,'distinct'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }

        $event = Event::find($event_id);
        if(!$event)
            return ApiResponse::sendResponse(404 , 'event not found' , []);

        if($request->user()->id != $event->user_id)
        {
            return ApiResponse::sendResponse(403 , 'you are not allowed to invite in this event' , []);
        }
        $inviter_id = $request->user()->id;

        $sucess_invitations = [];
        $failed_invitations = [];
        foreach ($request->ids as $invitee_id) 
        {
            $user = User::find($invitee_id);
            if($user->role == 'provider')
            {
                $failed_invitations[] = ['name' =>  $user->name , 'reason' => 'is provider not user'];
                continue;
            }
            $exist = EventInvitation::where('event_id', $event_id)
                ->where('invitee_id', $invitee_id)
                ->exists();

            if ($exist) 
            {
                $failed_invitations[] = ['name' => $user->name , 'reason' => ' already invited'];
                continue; 
            }

            $event->invitations()->create([
                'inviter_id' => $inviter_id,
                'invitee_id' => $invitee_id,
            ]);
            $sucess_invitations[] = ['name' => $user->name];
            
        }

        if(count($failed_invitations) && count($sucess_invitations))
        {
            return ApiResponse::sendResponse(200 , 'some invitation failed' , ['failed_invitation' => $failed_invitations]);
        }
        elseif(count($failed_invitations))
        {
            return ApiResponse::sendResponse(200 , 'All invitation failed' , ['failed_invitation' => $failed_invitations]);
        }
        elseif(count($sucess_invitations))
        {
            return ApiResponse::sendResponse(200 , 'All invitation end successfully' , []);
        }

    }

    public function index_guests(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['in:pending,accepted,rejected'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $event = Event::findOrFail($event_id);


        $invitations = $event->invitations()->with('invitee')->get();
        
        if($request->has('status'))
        {
            $invitations = $event->invitations()->with('invitee')->where('status' , $request->status)->get();
        }

        $data = $invitations->map(function($invitation)
        {
            return [
                'id' => $invitation->id,
                'invitee_id' => $invitation->invitee_id??null,
                'invitee_name' => $invitation->invitee->name??null,
                'status' => $invitation->status
            ];
        });

        return ApiResponse::sendResponse(200 , 'invitations retrieved successfully' , $data);
    }

    public function my_invitations(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'status' => ['in:pending,accepted,rejected'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $user = $request->user();

        $invitations = $user->recievedInvitations()->with('event.user')->get();
        
        if($request->has('status'))
        {
            $invitations = $user->recievedInvitations()->with('event.user')->where('status' , $request->status)->get();    
        }

        $invitations_data = $invitations->map(function($invitation){
            return [
                'id' => $invitation->id,
                'inviter_name' => $invitation->event->user->name,
                'status' => $invitation->status,
                'event' => new EventMainResource($invitation->event)
            ];
        }); 

        return ApiResponse::sendResponse(200 , 'invitations retrieved successfully' , $invitations_data);

        
    }


    public function my_events(Request $request)
    {
        $user = $request->user();
        $events = $user->events;

        if(count($events) < 1)
        {
            return ApiResponse::sendResponse( 200 , 'you dont have any event' , []);
        }
        return ApiResponse::sendResponse(200 , 'events retrieved successfully' , EventMainResource::collection($events));
    }


    public function confirm_invitation(Request $request , $invitation_id)
    {
        $validator = Validator::make($request->all(), [
            'confirm' => ['required', 'boolean'],
        ], [], []);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'verify Validation Errors', $validator->messages()->all());
        }
        $invitation = EventInvitation::find($invitation_id);
        if(!$invitation)
        {
            return ApiResponse::sendResponse(404 , 'invitation not found' , []);
        }
        if($request->user()->id != $invitation->invitee_id)
        {
            return ApiResponse::sendResponse(403 , 'this invitation is not for you ' , ['is_allowed' => false]);
        }

        if($invitation->status != 'pending')
        {
            return ApiResponse::sendResponse(409 , 'invitation is already confirmed before' , []);
        }
        $status = $request->confirm? 'accepted' : 'rejected';

        $invitation->update(['status' => $status]);

        return ApiResponse::sendResponse(200 , 'invitation confirmed successfully' , $invitation);
    }

}
