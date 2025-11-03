<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\StoreImage;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();

        $totalservices = Service::count();

        return view('admin.services' , compact('services' , 'totalservices'));
    }

    public function create()
    {
        $categories = Category::get();
        return view('admin.serviceCreate' , compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $image = StoreImage::upload($request->image , 'services');

        Service::create([
            'name' => $request->name,
            'image' => $image,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('dashboard.services.index')->with('success', 'Service created successfully!');
    }

    public function edit($service_id)
    {
        $categories = Category::get();
        $service = Service::findOrFail($service_id);

        return view('admin.serviceEdit' , compact('service' , 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->only('name', 'category_id');

        // لو الصورة اتحدثت
        if ($request->hasFile('image')) {
            $path = StoreImage::upload($request->image  , 'services');
            $data['image'] = $path;
        }

        $service->update($data);

        return redirect()->route('dashboard.services.index')->with('success', 'Service updated successfully!');
    }
    
    public function destroy($service_id)
    {
        $service = Service::findOrFail($service_id);
        
        $service->delete();
        
        
        return redirect()->route('dashboard.services.index')->with('success', 'Service deleted successfully!');
    }
}
