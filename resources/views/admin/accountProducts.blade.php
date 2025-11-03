@extends('adminlte::page')

@section('title', 'منتجات المستخدم')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🛍️ منتجات المستخدم</h4>

            {{-- لو عنده شركة --}}
            @if (isset($products))
                <a href="{{ route('createaccountproduct', $account_id) }}" class="btn btn-success btn-sm">
                    ➕ إضافة منتج
                </a>
            @endif
        </div>

        <div class="card-body">
            {{-- لو المستخدم معندوش شركة --}}
            @if (!isset($products))
                <div class="alert alert-warning text-center">
                    🚫 هذا المستخدم لا يمتلك شركة حاليًا.
                </div>
            @else
                {{-- لو عنده منتجات --}}
                @if ($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>image</th>
                                    <th>Name</th>
                                    <th>price</th>
                                    <th>description</th>
                                    <th>operation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <img src="{{ asset($product->main_image) }}" 
                                                alt="{{ $product->name }}" 
                                                width="80" height="80" 
                                                class="rounded border">
                                        </td>
                                        <td>{{ $product->title }}</td>
                                        <td>{{ $product->price ?? 'انظر أسعار الخيارات' }} </td>
                                        <td>{{ Str::limit($product->description, 50) }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- تعديل --}}
                                                <a href="" 
                                                class="btn btn-warning btn-sm">
                                                    ✏️ تعديل
                                                </a>

                                                {{-- حذف --}}
                                                <form action="{{ route('destroyaccountproduct' ,  ['account_id' => $account_id, 'product_id' => $product->id]) }}" 
                                                    method="POST" 
                                                    onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المنتج؟')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        🗑️ حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        ℹ️ لا توجد منتجات حاليًا.
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
