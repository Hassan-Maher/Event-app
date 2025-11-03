@extends('adminlte::page')

@section('title', 'تفاصيل الطلب')

@section('content_header')
    <h1 class="text-center mb-4">📦 Details Of Event Number #{{ $event->id }}</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🟢 Order Basic Info --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="text-muted mb-0">📋 Event Data</h5>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4"><strong>User Name:</strong> {{ $event->user->name }}</div>
                <div class="col-md-4"><strong>Name:</strong> {{ $event->name }}</div>
                <div class="col-md-4"><strong>Number Of Guests:</strong> {{ $event->number_of_guests }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Date:</strong> {{ $event->date }} </div>
            </div>
            <div class="row">
           
                <div class="col-md-8 text-end text-muted">
                    <small>📅 Created at: {{ $event->created_at->format('Y-m-d H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 🧑‍💼 User Info --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="text-muted mb-0">👤 User Data</h5>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $event->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $event->user->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $event->user->phone ?? 'N/A' }}</p>
        </div>
    </div>

    {{-- 🧾 Items --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="text-muted mb-0">🛍️ Event Items</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Company</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($item->product)
                                    {{ $item->product->title }}
                                @elseif($item->package)
                                    {{ $item->package->name }}
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td>{{ $item->product ? 'Product' : 'Package' }}</td>
                            <td>{{ $item->price }} ج.م</td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>{{ $item->store->name ?? 'N/A' }}</td>
                        </tr>

                        {{-- 🧩 تفاصيل الـ Item --}}
                        <tr class="bg-light">
                            <td colspan="6">
                                @if($item->product)
                                    <div class="p-3">
                                        <h6 class="text-primary mb-2">🛒 Product Details</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <img src="{{ asset($item->product->main_image) }}" 
                                                    alt="Product Image" class="img-fluid rounded shadow-sm">
                                            </div>
                                            <div class="col-md-9">
                                                <p><strong>Title:</strong> {{ $item->product->title }}</p>
                                                <p><strong>Description:</strong> {{ $item->product->description ?? 'N/A' }}</p>
                                                <p><strong>Price:</strong> {{ $item->product->price }} ج.م</p>
                                                <p><strong>Available:</strong> 
                                                    From {{ $item->product->available_from }} 
                                                    to {{ $item->product->available_to }}
                                                </p>
                                                <p><strong>Available Days:</strong> 
                                                    {{ implode(', ', $item->product->available_days) }}
                                                </p>

                                                {{-- 💠 Option Details --}}
                                                @if($item->option)
                                                    <div class="border-top pt-2 mt-2">
                                                        <h6 class="text-info">⚙️ Option Selected</h6>
                                                        <p><strong>Name:</strong> {{ $item->option->name }}</p>
                                                        <p><strong>Price:</strong> {{ $item->option->price }} ج.م</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item->package)
                                    <div class="p-3">
                                        <h6 class="text-success mb-2">🎁 Package Details</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <img src="{{ asset($item->package->image) }}" 
                                                    alt="Package Image" class="img-fluid rounded shadow-sm">
                                            </div>
                                            <div class="col-md-9">
                                                <p><strong>Name:</strong> {{ $item->package->name }}</p>
                                                <p><strong>Description:</strong> {{ $item->package->description ?? 'N/A' }}</p>
                                                <p><strong>Price:</strong> {{ $item->package->price }} ج.م</p>
                                                <p><strong>Offer:</strong> {{ $item->package->offer ?? '0' }}%</p>
                                                <p><strong>Final Price:</strong> {{ $item->package->final_price }} ج.م</p>
                                                <p><strong>End Date:</strong> {{ $item->package->end_date }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
