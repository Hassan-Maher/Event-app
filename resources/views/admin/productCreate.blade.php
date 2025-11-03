@extends('adminlte::page')

@section('title', 'إضافة منتج جديد')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">➕ إضافة منتج جديد</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('storeaccountproduct' , $account_id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- الصورة الرئيسية --}}
                <div class="mb-3">
                    <label class="form-label">الصورة الرئيسية <span class="text-danger">*</span></label>
                    <input type="file" name="main_image" class="form-control" accept="image/*" required>
                    @error('main_image') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- الصور الإضافية --}}
                <div class="mb-3">
                    <label class="form-label">صور إضافية</label>
                    <input type="file" name="extra_images[]" class="form-control" multiple accept="image/*">
                    @error('extra_images.*') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- اسم الخدمة --}}
                <div class="mb-3">
                    <label class="form-label">الخدمة <span class="text-danger">*</span></label>
                    <select name="service_id" class="form-select" required>
                        <option value="">-- اختر الخدمة --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- العنوان --}}
                <div class="mb-3">
                    <label class="form-label">العنوان <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
                    @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- الوصف --}}
                <div class="mb-3">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- السعر --}}
                <div class="mb-3">
                    <label class="form-label">السعر</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="form-control" min="1" step="0.01">
                    @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- المدينة --}}
                <div class="mb-3">
                    <label class="form-label">المدينة <span class="text-danger">*</span></label>
                    <select name="city_id" class="form-select" required>
                        <option value="">-- اختر المدينة --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- الأيام المتاحة --}}
                <div class="mb-3">
                    <label class="form-label">الأيام المتاحة <span class="text-danger">*</span></label>
                    <select name="available_days[]" class="form-select" multiple required>
                        <option value="السبت">السبت</option>
                        <option value="الأحد">الأحد</option>
                        <option value="الاثنين">الاثنين</option>
                        <option value="الثلاثاء">الثلاثاء</option>
                        <option value="الأربعاء">الأربعاء</option>
                        <option value="الخميس">الخميس</option>
                        <option value="الجمعة">الجمعة</option>
                    </select>
                    @error('available_days') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- الوقت من وإلى --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">من الساعة <span class="text-danger">*</span></label>
                        <input type="text" name="available_from" value="{{ old('available_from') }}" class="form-control" placeholder="مثلاً: 09:00 AM" required>
                        @error('available_from') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">إلى الساعة <span class="text-danger">*</span></label>
                        <input type="text" name="available_to" value="{{ old('available_to') }}" class="form-control" placeholder="مثلاً: 05:00 PM" required>
                        @error('available_to') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                {{-- الخيارات (Optional Options) --}}
                <div class="mb-3">
                    <label class="form-label">الخيارات (اختياري)</label>
                    <div id="options-container">
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <input type="text" name="options[0][name]" class="form-control" placeholder="اسم الخيار">
                            </div>
                            <div class="col-md-5">
                                <input type="number" name="options[0][price]" class="form-control" placeholder="السعر" min="1" step="0.01">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-option" class="btn btn-outline-primary btn-sm">➕ إضافة خيار</button>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success px-4">💾 حفظ المنتج</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">↩️ رجوع</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- سكربت لإضافة خيارات جديدة --}}
@section('js')
<script>
    document.getElementById('add-option').addEventListener('click', function () {
        const container = document.getElementById('options-container');
        const index = container.children.length;
        const div = document.createElement('div');
        div.classList.add('row', 'mb-2');
        div.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="options[${index}][name]" class="form-control" placeholder="اسم الخيار">
            </div>
            <div class="col-md-5">
                <input type="number" name="options[${index}][price]" class="form-control" placeholder="السعر" min="1" step="0.01">
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-sm remove-option">🗑️</button>
            </div>
        `;
        container.appendChild(div);

        div.querySelector('.remove-option').addEventListener('click', () => div.remove());
    });
</script>
@endsection

@endsection
