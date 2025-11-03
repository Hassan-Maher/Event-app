<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get();
        $totalpackages  = Package::count();

        return view('admin.packages' , compact('totalpackages' , 'packages'));
    }


    public function show($id)
    {
        $package = Package::with(['product.service','product.options','store.provider'])->findOrFail($id);

        return view('admin.packageShow', compact('package'));
    }
}
