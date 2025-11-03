@extends('adminlte::page')

@section('title', 'تفاصيل البروفايدر')

@section('content_header')
    <h1 class="text-center mb-4" style="font-size: 2rem;">👤  provider page</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🧍 القسم الأول: البيانات الشخصية --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-0">
            <h4 class="mb-0 text-muted">📋  Personal Data</h4>
        </div>
        <div class="card-body" style="font-size: 1.1rem;">
            <p><strong>Name:</strong> {{ $provider->name }}</p>
            <p><strong>Email :</strong> {{ $provider->email }}</p>
            <p><strong> phone:</strong> {{ $provider->phone ?? 'غير متوفر' }}</p>
            <p><strong> count created at:</strong> {{ $provider->created_at->format('Y-m-d') }}</p>
        </div>
    </div>

    {{-- 🏢 القسم الثاني: بيانات الشركة --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-0">
            <h4 class="mb-0 text-muted">🏢  Company Data</h4>
        </div>
        <div class="card-body" style="font-size: 1.1rem;">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    @if($store && $store->logo)
                        <img src="{{ asset($store->logo) }}" alt="Logo" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                    @else
                        <img src="https://via.placeholder.com/150" alt="No Logo" class="img-fluid rounded shadow-sm">
                    @endif
                </div>
                <div class="col-md-9">
                    <p><strong> Name:</strong> {{ $store->name ?? 'غير متوفر' }}</p>
                    <p><strong>Commercial Number :</strong> {{ $store->commercial_number ?? 'لا يوجد رقم.' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ⭐ القسم الثالث: التقييمات --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-muted">⭐ Evaluations</h4>
            <span class="badge bg-primary fs-5">إجمالي: {{ $evaluations->count() }}</span>
        </div>
        <div class="card-body">

            <div class="row text-center mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-body">
                            <h5 class="text-success"> Good Rates</h5>
                            <h2>{{ $goodEvaluations->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body">
                            <h5 class="text-danger"> Bad Rates</h5>
                            <h2>{{ $badEvaluations->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- عرض جميع التقييمات --}}
            <div class="row">
                @forelse($evaluations as $eval)
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body" style="font-size: 1.05rem;">
                                <h6 class="text-primary mb-1">{{ $eval->user->name ?? 'مستخدم مجهول' }}</h6>
                                <p class="mb-1">{{ $eval->description }}</p>
                                <div class="text-warning">
                                    @for($i=0; $i<$eval->rating; $i++)
                                        ⭐
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $eval->created_at->format('Y-m-d') }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">لا توجد تقييمات بعد.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 🛍️ القسم الرابع: المنتجات --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-0">
            <h4 class="mb-0 text-muted">🛍️ المنتجات</h4>
        </div>
        <div class="card-body text-center">
            <div class="card border-primary shadow-sm d-inline-block px-5 py-3">
                <h5 class="text-primary">عدد المنتجات</h5>
                <h2>{{ $total_products }}</h2>
            </div>
        </div>
    </div>

    {{-- 📦 القسم الخامس: الباكدجات --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-light border-0">
            <h4 class="mb-0 text-muted">📦 الباكدجات</h4>
        </div>
        <div class="card-body text-center">
            <div class="card border-info shadow-sm d-inline-block px-5 py-3">
                <h5 class="text-info">عدد الباكدجات</h5>
                <h2>{{ $total_packages }}</h2>
            </div>
        </div>
    </div>

</div>
@endsection
