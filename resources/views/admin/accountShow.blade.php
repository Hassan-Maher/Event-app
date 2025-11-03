@extends('adminlte::page')

@section('title', 'تفاصيل الحساب')

@section('content')
<div class="container mt-5">

    {{-- بيانات المستخدم الأساسية --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">👤  Account Data</h4>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong> Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        </div>
    </div>

    @if($user->role === 'provider')
        {{-- بيانات الشركة --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">🏢  Company Data</h5>
            </div>
            <div class="card-body">
                @if($user->store)
                    <p><strong> Company Name:</strong> {{ $user->store->name }}</p>
                    <p><strong>Commercial Number:</strong> {{ $user->store->commercial_number }}</p>
                @else
                    <p class="text-muted">لا يوجد شركة حاليًا.</p>
                    <a href="{{ route('company.create' , $user->id) }}" class="btn btn-outline-success">➕ إكمال بيانات الشركة</a>
                @endif
            </div>
        </div>

        {{-- الكروت الخاصة بالبروفايدر --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="{{ route('indexaccountproducts' , $user->id) }}" class="text-decoration-none">
                    <div class="card text-center shadow-sm hover-card">
                        <div class="card-body">
                            <h5>🛍️  Total Products</h5>
                            <h2>{{  $user->store?->product()->count() ?? 0  }}</h2>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="" class="text-decoration-none">
                    <div class="card text-center shadow-sm hover-card">
                        <div class="card-body">
                            <h5>🎁 Total Packages </h5>
                            <h2>{{ $user->store?->package()->count()??0 }}</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    @elseif($user->role === 'beneficiary')
        {{-- كروت المستخدم العادي --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="" class="text-decoration-none">
                    <div class="card text-center shadow-sm hover-card">
                        <div class="card-body">
                            <h5>🛒  Total Orders</h5>
                            <h2>{{ $user->orders()->count() }}</h2>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="" class="text-decoration-none">
                    <div class="card text-center shadow-sm hover-card">
                        <div class="card-body">
                            <h5>🎉  Total Events</h5>
                            <h2>{{ $user->events()->count() }}</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif

</div>

{{-- شوية تنسيقات بسيطة --}}
<style>
.hover-card {
    transition: 0.3s;
}
.hover-card:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection
