<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        
        if ($request->search) 
        {
            $users = User::where('role', 'beneficiary')->where(function ($query) use($request){
                $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
            })->get();
        }
        else {
            $users = User::where('role', 'beneficiary')->latest()->get();
        }

        $total_users = User::where('role' , 'beneficiary')->count();
        $active_users = User::where(['role' => 'beneficiary' , 'is_active' => true])->count();
        $blocked_users = User::where(['role' => 'beneficiary' , 'is_active' => false])->count();

        return view('admin.users' , compact(['users' , 'total_users' , 'active_users' , 'blocked_users']));
    }


    public function block($user_id)
    {
        $user = User::findOrFail($user_id);

        if($user)
            $user->update(['is_active' => false]);
        return to_route('dashboard.users.index');
    }
    public function active($user_id)
    {
        $user = User::findOrFail($user_id);
        
        if($user)
            $user->update(['is_active' => true]);

        return to_route('dashboard.users.index');
    }


    public function show($user_id)
    {
        $user = User::findOrFail($user_id);

        $total_orders = $user->orders()->count();
        $success_orders = $user->orders()->where('status' , 'accepted')->count();
        $failed_orders = $user->orders()->where('status'  , 'rejected')->count();

        $total_spent = $user->orders()->where('status' , 'accepted')->sum('final_price');

        $eventscount = $user->events()->count();

        return view('admin.userShow' , compact(['user' , 'total_orders' , 'success_orders' , 'failed_orders' , 'eventscount' ,'total_spent']));

    }
}
