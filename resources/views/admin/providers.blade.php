@extends('adminlte::page')

@section('title', 'المستخدمين')

@section('content_header')
    <h1 class="text-center mb-4">👥 إدارة المزودين</h1>
@endsection

@section('content')
<div class="container">
    {{-- الكروت --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light text-center p-3 rounded-3">
                <h5 class="text-muted"> total providers</h5>
                <h2 class="fw-bold text-dark">{{ $total_providers ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light text-center p-3 rounded-3">
                <h5 class="text-muted"> Active Providers</h5>
                <h2 class="fw-bold text-success">{{ $active_providers ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light text-center p-3 rounded-3">
                <h5 class="text-muted">Blocked Providers</h5>
                <h2 class="fw-bold text-danger">{{ $blocked_providers ?? 0 }}</h2>
            </div>
        </div>
    </div>

    {{-- الجدول --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-light">
            <h5 class="mb-0 text-muted">قائمة المزودين</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th> Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Operation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($providers as $index => $provider)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $provider->name }}</td>
                            <td>{{ $provider->email }}</td>
                            <td>{{ $provider->phone }}</td>
                            <td>
                                @if($provider->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Blocked</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('provider.show' , $provider->id) }}" class="btn btn-sm btn-outline-primary">
                                    التفاصيل
                                </a>
                                @if($provider->is_active)
                                    <form action="{{ route('provider.block' , $provider->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            حظر
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('provider.active' , $provider->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="6" class="text-muted">لا يوجد مزودين حاليًا</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
