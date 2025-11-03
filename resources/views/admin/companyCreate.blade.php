@extends('adminlte::page')

@section('title', 'إضافة شركة جديدة')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🏢 إضافة شركة جديدة</h4>
        </div>

        <div class="card-body">
            {{-- عرض الأخطاء --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- الفورم --}}
            <form action="{{ route('company.store' , $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- اسم الشركة --}}
                <div class="mb-3">
                    <label for="name" class="form-label">اسم الشركة</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name') }}" required>
                </div>

                {{-- الشعار (اللوجو) --}}
                <div class="mb-3">
                    <label for="logo" class="form-label">شعار الشركة</label>
                    <input type="file" name="logo" id="logo" class="form-control" accept="image/*" required>
                </div>

                {{-- الرقم التجاري --}}
                <div class="mb-3">
                    <label for="commercial_number" class="form-label">الرقم التجاري</label>
                    <input type="text" name="commercial_number" id="commercial_number" class="form-control"
                        value="{{ old('commercial_number') }}" required>
                </div>

                {{-- الإحداثيات --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">خط العرض (Latitude)</label>
                        <input type="number" step="any" name="latitude" id="latitude" class="form-control"
                            value="{{ old('latitude') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">خط الطول (Longitude)</label>
                        <input type="number" step="any" name="longitude" id="longitude" class="form-control"
                            value="{{ old('longitude') }}" required>
                    </div>
                </div>

                {{-- المدينة --}}
                <div class="mb-3">
                    <label for="city_id" class="form-label">المدينة</label>
                    <select name="city_id" id="city_id" class="form-select" required>
                        <option value="">اختر المدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- زرار الحفظ --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        💾 حفظ الشركة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
