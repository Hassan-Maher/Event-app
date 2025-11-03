@extends('adminlte::page')

@section('title', 'تفاصيل الباكدج')

@section('content_header')
    <h1 class="text-center mb-4" style="font-size: 2rem;">🎁  package details</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🔹 القسم الأول: بيانات الباكدج --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📦  Package data</h5>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong> Name:</strong> {{ $package->name }}</p>
                <p><strong> Main Price:</strong> {{ $package->price }} جنيه</p>
                <p><strong>offer:</strong> {{ $package->offer ? $package->offer . ' جنيه' : 'لا يوجد خصم' }}</p>
                <p><strong> final Price:</strong> {{ $package->final_price }} جنيه</p>
            </div>
            <div class="col-md-6">
                <p><strong>description:</strong> {{ $package->description ?? 'لا يوجد وصف' }}</p>
                <p><strong> End date:</strong> {{ $package->end_date->format('Y-m-d') }}</p>
                <p><strong> created date:</strong> {{ $package->created_at->format('Y-m-d') }}</p>
            </div>
        </div>
    </div>

    {{-- 🔹 القسم الثاني: المنتجات داخل الباكدج --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">🛍️ products in Package</h5>
        </div>
        <div class="card-body">
            @if ($package->product->count() > 0)
                <div class="row">
                    @foreach ($package->product as $product)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="{{ asset($product->main_image) }}" class="card-img-top rounded-top" alt="Product Image">
                                <div class="card-body">
                                    <h5 class="card-title text-center">{{ $product->title }}</h5>
                                    <p class="text-center text-muted mb-2">{{ $product->service->name ?? '—' }}</p>

                                    {{-- السعر الأساسي --}}
                                    <p class="text-center fw-bold mb-2">{{ $product->price }} جنيه</p>

                                    {{-- ✅ لو فيه option --}}
                                    @if ($product->pivot->option_id)
                                        @php
                                            $option = $product->options->where('id', $product->pivot->option_id)->first();
                                        @endphp
                                        @if ($option)
                                            <div class="bg-light p-2 rounded border mt-2">
                                                <p class="mb-1"><strong>🧩 Option Name:</strong> {{ $option->name }}</p>
                                                <p class="mb-0"><strong>💰 Option Price:</strong> {{ $option->price }} جنيه</p>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="text-center mt-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted mb-0">🚫 لا توجد منتجات داخل هذه الباكدج.</p>
            @endif
        </div>
    </div>

    {{-- 🔹 القسم الثالث: بيانات الشركة والبروفايدر --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">🏢   provider and company data</h5>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong> name:</strong> {{ $package->store->name }}</p>
                <p><strong> commercial_number:</strong> {{ $package->store->commercial_number ?? 'غير متوفر' }}</p>
                <p><strong> created date :</strong> {{ $package->store->created_at->format('Y-m-d') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong> provider name:</strong> {{ $package->store->provider->name ?? 'غير متوفر' }}</p>
                <p><strong>Email:</strong> {{ $package->store->provider->email ?? 'غير متوفر' }}</p>
                <p><strong> Phone:</strong> {{ $package->store->provider->phone ?? 'غير متوفر' }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
