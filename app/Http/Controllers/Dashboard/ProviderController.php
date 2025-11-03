<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
      public function index()
    {
        $providers = User::where('role', 'provider')->latest()->get();

        $total_providers = User::where('role' , 'provider')->count();
        $active_providers = User::where(['role' => 'provider' , 'is_active' => true])->count();
        $blocked_providers = User::where(['role' => 'provider' , 'is_active' => false])->count();

        return view('admin.providers' , compact(['providers' , 'total_providers' , 'active_providers' , 'blocked_providers']));
    }

    public function block($provider_id)
    {
        $provider = User::findOrFail($provider_id);

        if($provider)
            $provider->update(['is_active' => false]);
        return to_route('dashboard.providers.index');
    }
    public function active($provider_id)
    {
        $provider = User::findOrFail($provider_id);
        
        if($provider)
            $provider->update(['is_active' => true]);

        return to_route('dashboard.providers.index');
    }


    public function show($user_id)
    {
        $provider = User::findOrFail($user_id);

        $store = $provider->store;

        $total_products = $store->product()->count();

        $total_packages  = $store->package()->count();

        $evaluations = $store->evaluations;

        $goodEvaluations = $store->evaluations()->where('rating' , '>=' , 3)->get();
        $badEvaluations  = $store->evaluations()->where('rating' , '<' , 3 )->get();

        
    

        return view('admin.providerShow' , compact(['provider' , 'store' , 'total_products' , 'total_packages' ,'evaluations' , 'goodEvaluations' , 'badEvaluations']));

    }
}
