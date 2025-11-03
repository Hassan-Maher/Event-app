@extends('adminlte::page')

@section('title', 'قائمة الباقات')

@section('content_header')
    <h1 class="text-center mb-4 fw-bold" style="font-size: 2.2rem;">📦 Packages Data</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🔹 الكروت الإحصائية --}}
    <div class="row text-center mb-4 justify-content-center">
        <div class="col-md-4 mb-3">
            <div class="card border-primary shadow-lg rounded-3">
                <div class="card-body">
                    <h5 class="text-primary mb-2 fw-semibold">Total Packages</h5>
                    <h2 class="fw-bold">{{ $totalpackages }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 جدول المنتجات --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-muted fw-semibold">🧾 Packages List</h4>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>📝 Name</th>
                        <th>🖼️  Image</th>
                        <th>📅 Created Date</th>
                        <th>  Final Price </th>
                        <th>🏢 Company Name</th>
                        <th>⚙️ Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($packages as $package)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $package->name }}</td>
                            <td>
                                <img src="{{ asset($package->image) }}" 
                                    alt="package Image"
                                    class="img-thumbnail rounded shadow-sm"
                                    style="width: 90px; height: 90px; object-fit: cover;">
                            </td>
                            <td>{{ $package->created_at->format('Y-m-d') }}</td>
                            <td>{{ $package->final_price }}</td>
                            <td>{{ $package->store->name }}</td>
                            <td>
                                <a href="{{ route('package.show' , $package->id) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    🔍 View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4">
                                🚫 No pacakges available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
