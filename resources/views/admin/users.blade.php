@extends('adminlte::page')

@section('title', 'المستخدمين')

@section('content_header')
    <h1 class="text-center mb-4">👥 إدارة المستخدمين</h1>
@endsection

@section('content')
<div class="container">
    {{-- الكروت --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light text-center p-3 rounded-3">
                <h5 class="text-muted"> total users</h5>
                <h2 class="fw-bold text-dark">{{ $total_users ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light text-center p-3 rounded-3">
                <h5 class="text-muted"> Active Users</h5>
                <h2 class="fw-bold text-success">{{ $active_users ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light text-center p-3 rounded-3">
                <h5 class="text-muted">Blocked Users</h5>
                <h2 class="fw-bold text-danger">{{ $blocked_users ?? 0 }}</h2>
            </div>
        </div>
    </div>

    {{-- الجدول --}}
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0 text-muted">قائمة المستخدمين</h5>

        {{-- مربع البحث --}}
        <form action="{{ route('dashboard.users.index') }}" method="GET" class="d-flex mt-2 mt-md-0" role="search">
            <input 
                type="text" 
                name="search" 
                class="form-control me-2 rounded-pill" 
                placeholder="🔍 ابحث بالاسم أو الإيميل او بالتلفون ..." 
                value="{{ request('search') }}"
                style="min-width: 220px;"
            >
            <button class="btn btn-primary rounded-pill px-3" type="submit">
                بحث
            </button>
        </form>
    </div>

    <div class="card-body">
        <table class="table table-hover text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Blocked</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('user.show' , $user->id) }}" class="btn btn-sm btn-outline-primary">
                                التفاصيل
                            </a>
                            @if($user->is_active)
                                <form action="{{ route('user.block' , $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        حظر
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('user.active' , $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        تفعيل
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">لا يوجد مستخدمين حاليًا</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
