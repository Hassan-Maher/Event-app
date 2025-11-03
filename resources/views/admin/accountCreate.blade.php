@extends('adminlte::page')

@section('title', 'إضافة حساب جديد')

@section('content_header')
    <h1 class="text-center mb-4">➕ إضافة حساب جديد</h1>
@endsection

@section('content')
<div class="container">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('account.store') }}" method="POST">
                @csrf

                {{-- 🧍‍♂️ الاسم --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">الاسم</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="أدخل اسم المستخدم">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 📧 البريد الإلكتروني --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="example@email.com">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 📱 رقم الهاتف --}}
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">رقم الهاتف</label>
                    <input type="text" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}" placeholder="+201000000000">
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 🔐 كلمة المرور --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">كلمة المرور</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="********">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 🔒 تأكيد كلمة المرور --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" placeholder="أعد كتابة كلمة المرور">
                </div>

                {{-- 🧩 نوع الحساب --}}
                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">نوع الحساب</label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">-- اختر نوع الحساب --</option>
                        <option value="beneficiary" {{ old('role') == 'beneficiary' ? 'selected' : '' }}>beneficiary</option>
                        <option value="provider" {{ old('role') == 'provider' ? 'selected' : '' }}>provider</option>
                    </select>
                    @error('role')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 🧾 الأزرار --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">⬅️ رجوع</a>
                    <button type="submit" class="btn btn-primary">💾 حفظ الحساب</button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    .card {
        max-width: 600px;
        margin: auto;
    }
</style>
@endsection
