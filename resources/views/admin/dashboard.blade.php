@extends('adminlte::page')

@section('title', 'لوحة التحكم')

@section('content_header')
    <h1> Hello Admin 🎉</h1>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row g-4">

        {{-- 🔹 الكروت الإحصائية --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 card-hover h-100">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Users</h6>
                            <h3 class="fw-bold mb-0">{{ $users }}</h3>
                        </div>
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 card-hover h-100">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Providers</h6>
                            <h3 class="fw-bold mb-0">{{ $providers }}</h3>
                        </div>
                        <i class="fas fa-briefcase fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 card-hover h-100">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Products</h6>
                            <h3 class="fw-bold mb-0">{{ $products }}</h3>
                        </div>
                        <i class="fas fa-box fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 card-hover h-100">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Packages</h6>
                            <h3 class="fw-bold mb-0">{{ $packages }}</h3>
                        </div>
                        <i class="fas fa-gift fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 card-hover h-100">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Orders</h6>
                            <h3 class="fw-bold mb-0">{{ $orders }}</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 card-hover h-100">
                <div class="card-body bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Events</h6>
                            <h3 class="fw-bold mb-0">{{ $events }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 🔹 قسم الحسابات الخاصة بالأدمن --}}
    <div class="card shadow-sm border-0 mt-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-muted mb-0">👤 My Accounts</h4>
            <a href="{{ route('account.create') }}" class="btn btn-primary btn-sm fw-semibold">
                ➕ Add Account
            </a>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover text-center align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>🆔 #</th>
                        <th>👤 Name</th>
                        <th>📧 Email</th>
                        <th>📱 Phone</th>
                        <th>🏷️ Role</th>
                        <th>⚙️ Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adminaccounts as $account)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $account->name }}</td>
                            <td>{{ $account->email }}</td>
                            <td>{{ $account->phone ?? '-' }}</td>
                            <td>
                            
                                <span class="badge bg-primary">{{ $account->role }}</span>                               
                            </td>
                            <td>
                                <a href="{{ route('account.show' , $account->id) }}" class="btn btn-outline-info btn-sm">
                                    🔍 Details
                                </a>
                                <a href="{{ route('account.edit' , $account->id) }}" class="btn btn-outline-warning btn-sm">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('account.destroy' , $account->id) }}" method="POST" 
                                    style="display:inline" 
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                                    @csrf
                                    
                                    <button type="submit" class="btn btn-outline-danger btn-sm">🗑️ Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-3">🚫 No admin accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .card-hover {
        transition: all 0.3s ease-in-out;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
