@extends('adminlte::page')

@section('title', 'قائمة الخدمات')

@section('content_header')
    <h1 class="text-center mb-4 fw-bold" style="font-size: 2.2rem;">📦 Services Data</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🔹 الكروت الإحصائية --}}
    <div class="row text-center mb-4 justify-content-center">
        <div class="col-md-4 mb-3">
            <div class="card border-primary shadow-lg rounded-3">
                <div class="card-body">
                    <h5 class="text-primary mb-2 fw-semibold">Total Services</h5>
                    <h2 class="fw-bold">{{ $totalservices }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 زر الإضافة --}}
    <div class="mb-3 text-end">
        <a href="{{ route('service.create') }}" class="btn btn-success fw-semibold">
            ➕ Add New Service
        </a>
    </div>

    {{-- 🔹 جدول الخدمات --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-muted fw-semibold">🧾 Services List</h4>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>📝 Name</th>
                        <th>🖼️ Image</th>
                        <th>📅 Category</th>
                        <th>📊 Number of Products</th>
                        <th>⚙️ Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $service->name }}</td>

                            {{-- ✅ عرض الصورة مباشرة من المسار المخزن --}}
                            <td>
                                <img src="{{ asset($service->image) }}" 
                                    alt="Service Image"
                                    class="img-thumbnail rounded shadow-sm"
                                    style="width: 90px; height: 90px; object-fit: cover;">
                            </td>

                            <td>{{ $service->category->name ?? '-' }}</td>
                            <td>{{ $service->product->count() }}</td>

                            {{-- 🔹 أزرار العمليات --}}
                            <td class="text-center">
                                {{-- ✏️ زر التعديل --}}
                                <a href="{{ route('service.edit', $service->id) }}" 
                                class="btn btn-outline-warning btn-sm me-2">
                                    ✏️ Edit
                                </a>

                                {{-- 🗑️ زر الحذف --}}
                                <form action="{{ route('service.destroy', $service->id) }}" 
                                    method="POST" 
                                    style="display: inline;"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted py-4">
                                🚫 No services available yet.
                            </td>
                        </tr>
@endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
