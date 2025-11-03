@extends('adminlte::page')

@section('title', 'تفاصيل المنتج')

@section('content_header')
    <h1 class="text-center mb-4" style="font-size: 2rem;">🛍️  product details</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🔹 القسم الأول: بيانات الشركة والبروفايدر --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🏢   company and provider data</h5>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong> Name Of company:</strong> {{ $product->store->name }}</p>
                <p><strong>  commercial_number:</strong> {{ $product->store->commercial_number ?? 'غير متوفر' }}</p>
                <p><strong> created date :</strong> {{ $product->store->created_at ?? 'غير متوفر' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong> provider name:</strong> {{ $product->store->provider->name ?? 'غير متوفر' }}</p>
                <p><strong>Email:</strong> {{ $product->store->provider->email ?? 'غير متوفر' }}</p>
                <p><strong> phone:</strong> {{ $product->store->provider->phone }}</p>
            </div>
        </div>
    </div>

    {{-- 🔹 القسم الثاني: بيانات المنتج --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">📦  product Data</h5>
        </div>
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong>Name:</strong> {{ $product->title }}</p>
                <p><strong>Service:</strong> {{ $product->service->name }}</p>
                <p><strong>City:</strong> {{ $product->city->name ?? 'غير متوفرة' }}</p>
                <p><strong>Price:</strong> {{ $product->price ? $product->price . ' جنيه' : 'غير محدد' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Description:</strong> {{ $product->description ?? 'لا يوجد وصف' }}</p>
                <p><strong> Available Days:</strong> 
                    @foreach ($product->available_days as $day)
                        <span class="badge bg-secondary">{{ ucfirst($day) }}</span>
                    @endforeach
                </p>
                <p><strong>From:</strong> {{ $product->available_from }} <strong>To:</strong> {{ $product->available_to }}</p>
            </div>
        </div>
    </div>

    {{-- 🔹 القسم الثالث: الصور --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">🖼️  product images</h5>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- الصورة الرئيسية --}}
                <div class="col-md-4 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white text-center">الصورة الرئيسية</div>
                        <img src="{{ asset($product->main_image) }}" class="img-fluid rounded-bottom" alt="Main Image">
                    </div>
                </div>

                {{-- باقي الصور --}}
                @foreach($product->image as $image)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <img src="{{ asset($image->image) }}" class="img-fluid rounded" alt="Product Image">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 🔹 القسم الرابع: الخيارات --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">⚙️  product options</h5>
        </div>
        <div class="card-body">
            @if ($product->options->count() > 0)
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->options as $option)
                            <tr>
                                <td>{{ $option->name }}</td>
                                <td>{{ $option->price }} pound</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted text-center">🚫 لا توجد خيارات لهذا المنتج.</p>
            @endif
        </div>
    </div>

</div>
@endsection
