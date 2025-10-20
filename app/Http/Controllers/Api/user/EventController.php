<?php

namespace App\Http\Controllers\Api\user;

use App\Helpers\ApiResponse;
use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditItemRequest;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventItemResource;
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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

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
            foreach($request->items as $item)
        {
            if($item['type'] == 'product')
            {
                $product = Product::find($item['item_id']);
                if($product)
                {
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
            }
            elseif($item['type'] == 'package')
            {
                $package = Package::find($item['item_id']);                
                $item['store_id'] = $package->store_id;
                $item['price'] = $package->final_price;
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

    public function edit_item(EditItemRequest $request , $event_id)
    {
        $event = Event::findOrFail($event_id);
        if($event->user_id != $request->user()->id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);

        }
        $data = [];
            if($request['type'] == 'product')
            {
                $product = Product::find($request['item_id']);
                if($product)
                {
                    $data['item_id'] = $request->item_id;
                    $data['type'] = 'product';
                    $data['store_id'] = $product->store_id;
                    if(!empty($request->option_id))
                    {
                        $option = ProductOption::find($request['option_id']);
                        $data['option_id'] = $request->option_id;
                        $data['price'] = $option->price;
                    }
                    else{
                        $data['price'] = $product->price;
                    }
                    
                }
            }
            elseif($request['type'] == 'package')
            {
                $package = Package::find($request['item_id']);    
                $data['item_id'] = $request->item_id;
                $data['type'] = 'package';
                $data['store_id'] = $package->store_id;
                $data['price'] = $package->final_price;
            }

        $data['quantity'] = $request->quantity;

        $item = $event->items()->create($data);
        $item->load(['product' , 'package']);


        $event->update(['price' => $event->calculatePrice()]);

        return ApiResponse::sendResponse(201 , 'item stored successfully' , ['item' =>new  EventItemResource($item)]);
    }

    public function delete_item(Request $request , $id)
    {
        $item = EventItem::find($id);
        if(!$item)
            return ApiResponse::sendResponse(404 , 'item not found' , []);
        $event =$item->event;
        if($item->event->user_id != $request->user()->id)
        {
            return ApiResponse::sendResponse(403 , 'forbidden' , []);
        }
        if($item->status != 'pending')
        {     
            return ApiResponse::sendResponse(403 , 'item is not pending' , []);
        }

        $item->delete();

        $event->update(['price' => $event->calculatePrice()]);

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
            'users'         => ['required', 'array'],
            'users.*.name'  => ['string'],
            'users.*.phone' => ['string' , 'regex:/^\d{1,4}[0-9]{7,12}$/' , 'unique:event_invitations,invitee_phone'],
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


        $inviter = $request->user();

        $sucess_invitations = [];
        $failed_invitations = [];
        foreach ($request->users as $user) 
        {

            $exist = EventInvitation::where('event_id', $event_id)
                ->where('invitee_phone', $user['phone'])
                ->exists();

            if ($exist) 
            {
                $failed_invitations[] = ['name' => $user['name'] , 'reason' => ' already invited'];
                continue; 
            }
            if($request->user()->phone == $user['phone'])
            {
                $failed_invitations[] = ['name' => $user['name'] , 'reason' => ' you send to yourself'];
                continue;
            }

            $event->invitations()->create([
                'inviter_id' => $inviter->id,
                'invitee_name' => $user['name'],
                'invitee_phone' =>$user['phone'],
            ]);

            
            $sucess_invitations[] = [
                'name' => $user['name'],
                ];
                
        }

        $url = url("/api/invitations/event/{$event->id}/respond");
            
        $message = "🎉 دعوة من {$inviter->name} 🎉%0A"
        ."تمت دعوتك لحضور مناسبة: *{$event->title}*%0A%0A"
        ."للرد  على الدعوة اضغط هنا 👇%0 A{$url}%0A%0A";

        $whatsappLink = "https://wa.me/{$user['phone']}?text={$message}";

        if(count($failed_invitations) && count($sucess_invitations))
        {
            return ApiResponse::sendResponse(200 , 'some invitation failed' , ['failed_invitation' => $failed_invitations , 'success_invitations' => $sucess_invitations , 'whatsapp_link' => $whatsappLink , 'message' => $message]);
        }
        elseif(count($failed_invitations))
        {
            return ApiResponse::sendResponse(200 , 'All invitation failed' , ['failed_invitation' => $failed_invitations]);
        }
        elseif(count($sucess_invitations))
        {
            return ApiResponse::sendResponse(200 , 'All invitation send successfully' , [ 'success invitations' => $sucess_invitations , 'whatsapp_link' => $whatsappLink]);
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


        $invitations = $event->invitations;
        
        if($request->has('status'))
        {
            $invitations = $event->invitations()->where('status' , $request->status)->get();
        }

        if(count($invitations) < 1)
            return ApiResponse::sendResponse(404 , 'guests is emtpy' , []);
        $data = $invitations->map(function($invitation)
        {
            return [
                'id' => $invitation->id,
                'invitee_name' => $invitation->invitee_name??null,
                'invitee_phone' => $invitation->invitee_phone??null,
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
        $phone = ltrim($user->phone ,'+');
        
        $invitations = EventInvitation::where('phone' , $phone)->when($request->status , function ($query) use ($request)
        {
            $query->where('status' , $request->status);
        })->get();

        $invitations_data = $invitations->map(function($invitation){
            return [
                'id' => $invitation->id,
                'inviter' => $invitation->event->user->name,
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

    public function response(Request $request , $event_id)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required' , 'exists:event_invitations,invitee_phone'],
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return ApiResponse::sendResponse(422, 'Validation error', $validator->messages()->all());
        }

        $invitation = EventInvitation::where(['invitee_phone'=> $request->phone , 'event_id' => $event_id])->first();

        if(!$invitation)
            return ApiResponse::sendResponse(404 , 'invitation is not found' , []);
        
        if($invitation->status != 'pending')
            return ApiResponse::sendResponse(403 , 'invitation has been responsed' , ['is_response' => true]);

        $invitation->update([
            'status' => $request->status,
        ]);

        return ApiResponse::sendResponse(200, 'Invitation '.$request->status.' successfully', [
            'phone' => $invitation->invitee_phone,
            'status' => $invitation->status
        ]);
    }

}
