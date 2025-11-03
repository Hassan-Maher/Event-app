<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\City;
use App\Models\Event;
use App\Models\Order;
use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HomeController extends Controller
{
    public function index()
    {

        $users = User::where('role' , 'beneficiary')->count();
        $providers = User::where('role' , 'provider')->count();
        $products = Product::count();
        $orders = Order::count();
        $packages = Package::count();
        $events = Event::count();

        $adminaccounts = User::where('is_admin' , true)->get();
        

        return view('admin.dashboard' , compact(['users' , 'providers' , 'products' , 'events' , 'orders' , 'packages' , 'adminaccounts']));
    }

    public function create()
    {
        return view('admin.accountCreate');
    }

    public function store(RegisterRequest $request)
    {
        $validated_data = $request->validated();

        $validated_data['is_admin'] = true;

        $account = User::create($validated_data);

        return redirect()->route('dashboard')->with('success', 'تم إضافة الحساب بنجاح ✅');

    }

    public function edit($account_id)
    {
        $account = User::findOrFail($account_id);

        return view('admin.accountEdit' , compact('account'));
    }

    public function update(UpdateAccountRequest $request, $account_id)
    {
        $validated_data = $request->validated();

        $account = User::findOrFail($account_id);

        $validated_data['is_admin'] = true;

        $account->update($validated_data);

        return redirect()->route('dashboard')->with('success', 'تم تحديث الحساب بنجاح ✅');
    }

    public function destroy($account_id)
    {
        $account = User::findOrFail($account_id);

        $account->delete();

        return redirect()->route('dashboard');
    }

    public function show($account_id)
    {
        $user = User::with(['store' , 'store.product' , 'store.package' , 'orders' , 'events'])->findOrFail($account_id);

        return view('admin.accountShow' , compact('user'));
    }


    public function createcompany($account_id)
    {
        $cities = City::get();

        $user = User::findOrFail($account_id);
        return view('admin.companyCreate' , compact('user' , 'cities'));
    }

    public function storecompany(CompanyRequest $request , $account_id)
    {
        $validated_data = $request->validated();

        $user = User::findOrFail($account_id);
        $validated_data['logo'] = StoreImage::upload($request->logo , 'stores');

        $user->store()->create($validated_data);

        return redirect()->route('account.show' , $account_id);
    }

    public function indexproducts($account_id)
    {
        $user = User::findOrFail($account_id);
        $store = $user->store;
        $products = $store ? $store->product : null;

        return view('admin.accountProducts', compact('products', 'store' , 'account_id'));
    }

    public function createproduct($account_id)
    {
        $services = Service::all();
        $cities = City::all();

        return view('admin.productCreate'  , compact('services' , 'cities' , 'account_id'));
    }

    public function storeproduct(ProductRequest $request , $account_id)
    {
        $user = User::findOrFail($account_id);

        $store = $user->store;
        $validated_data = $request->validated();

        if($request->hasFile('main_image'))
        {
            $validated_data['main_image'] = StoreImage::upload($request->file('main_image') , 'products');
        }
            

        $validated_data['store_id'] = $store->id;

        $product = Product::create(Arr::except($validated_data, ['extra_images' , 'options']));

        if(!$product)
            return 'product failed to store';

    

        if ($request->hasFile('extra_images') && !empty($validated_data['main_image'])) 
        {
            $product->image()->create([
                'image'   => $validated_data['main_image'],
                'is_main' => true,
            ]);

                foreach($request->file('extra_images') as $image)
                {
                    
                    $image_path = StoreImage::upload($image , 'products');
                    $product->image()->create([
                        'image'   => $image_path,
                        'is_main' => false,
                    ]);
                }
        }

        if ($request->has('options') && is_array($validated_data['options']))
       {
            foreach ($validated_data['options'] as $index => $option) 
            {
                if (isset($option['name']) && isset($option['price'])) {
                    $product->options()->create([
                        'name'  => $option['name'],
                        'price' => $option['price'],
                    ]);
                }
            }
       }

       return redirect()->route('indexaccountproducts' , $account_id);
    }

    public function destroyproduct( $account_id,$product_id)
    {
        $product = Product::findOrFail($product_id);


        $product->delete();

        return redirect()->route('indexaccountproducts' , $account_id);
    }


}
