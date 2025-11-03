@extends('adminlte::page')

@section('title', 'إضافة خدمة جديدة')

@section('content_header')
    <h1 class="text-center mb-4 fw-bold" style="font-size: 2rem;">➕ Add New Service</h1>
@endsection

@section('content')
<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h4 class="mb-0 fw-semibold text-muted">🧾 Service Information</h4>
        </div>

        <div class="card-body">
            {{-- ✅ فورم الإضافة --}}
            <form action="{{ route('service.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 🔹 الاسم --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">📝 Service Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="Enter service name" 
                           value="{{ old('name') }}" 
                           required>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 🔹 الصورة --}}
                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">🖼️ Service Image</label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           class="form-control @error('image') is-invalid @enderror" 
                           accept="image/*" 
                           required>

                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 🔹 الفئة (Category) --}}
                <div class="mb-4">
                    <label for="category_id" class="form-label fw-semibold">📂 Select Category</label>
                    <select name="category_id" 
                            id="category_id" 
                            class="form-select @error('category_id') is-invalid @enderror" 
                            required>
                        <option value="">-- Choose a category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 🔹 الأزرار --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-primary fw-semibold px-4">💾 Save</button>
                    <a href="{{ route('dashboard.services.index') }}" class="btn btn-secondary fw-semibold px-4">↩️ Back</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
