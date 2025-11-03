@extends('adminlte::page')

@section('title', 'تفاصيل المستخدم')

@section('content_header')
    <h1 class="text-center mb-5 fw-bold">👤 User Details </h1>
@endsection

@section('content')
<div class="container">

    {{-- القسم الأول: البيانات الشخصية --}}
    <div class="card shadow-sm border-0 mb-5" style="background-color:#f8f9fa;">
        <div class="card-header bg-white border-0 pb-0">
            <h4 class="text-muted mb-3">📋 Personal Data</h4>
        </div>
        <div class="card-body pt-2">
            <div class="row g-4">
                <div class="col-md-6">
                    <p class="fs-5 mb-1"><strong>Name:</strong></p>
                    <p class="text-secondary">{{ $user->name }}</p>
                </div>
                <div class="col-md-6">
                    <p class="fs-5 mb-1"><strong> Email:</strong></p>
                    <p class="text-secondary">{{ $user->email }}</p>
                </div>
                <div class="col-md-6">
                    <p class="fs-5 mb-1"><strong> Phone:</strong></p>
                    <p class="text-secondary">{{ $user->phone }}</p>
                </div>
                <div class="col-md-6">
                    <p class="fs-5 mb-1"><strong>Status:</strong></p>
                    @if($user->is_active)
                        <span class="badge bg-success px-3 py-2">نشط</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2">غير نشط</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="fs-5 mb-1"><strong> Account Created At:</strong></p>
                    <p class="text-secondary">{{ $user->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- القسم الثاني: الطلبات --}}
    <div class="row mb-5 text-center g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color:#f1f3f5;">
                <h6 class="text-muted mb-2"> Total Orders</h6>
                <h3 class="fw-bold text-dark">{{ $total_orders ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color:#f1f3f5;">
                <h6 class="text-muted mb-2"> Success Orders</h6>
                <h3 class="fw-bold text-success">{{ $success_orders ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color:#f1f3f5;">
                <h6 class="text-muted mb-2"> Failed Orders</h6>
                <h3 class="fw-bold text-danger">{{ $failed_orders ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color:#f1f3f5;">
                <h6 class="text-muted mb-2"> Total Spent</h6>
                <h3 class="fw-bold text-primary">{{ number_format($total_spent ?? 0, 2) }} ج.م</h3>
            </div>
        </div>
    </div>

    {{-- القسم الثالث: المناسبات --}}
    <div class="card shadow-sm border-0 mb-5" style="background-color:#f8f9fa;">
        <div class="card-header bg-white border-0 pb-0">
            <h4 class="text-muted mb-3">🎉 Events</h4>
        </div>
        <div class="card-body text-center">
            <h5 class="fw-normal text-secondary">
                Number Of events: 
                <strong class="text-dark fs-4">{{ $eventscount ?? 0 }}</strong>
            </h5>
        </div>
    </div>

    {{-- القسم الرابع: تحليل النشاط بالذكاء الاصطناعي --}}
    {{-- <div class="card shadow-sm border-0 mb-5" style="background-color:#f8f9fa;">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 pb-0">
            <h4 class="text-muted mb-3">🤖 تحليل النشاط الذكي</h4>
            <form action="{{ route('users.analyze', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">تحديث التحليل</button>
            </form>
        </div>
        <div class="card-body pt-2">
            @if(isset($aiAnalysis))
                <p class="fs-5 text-dark mb-0">{{ $aiAnalysis }}</p>
            @else
                <p class="text-muted">لم يتم تحليل النشاط بعد، اضغط على زر "تحديث التحليل" أعلاه لبدء التحليل.</p>
            @endif
        </div>
    </div>

</div> --}}
@endsection
