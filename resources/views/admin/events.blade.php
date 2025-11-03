@extends('adminlte::page')

@section('title', 'قائمة المناسبات')

@section('content_header')
    <h1 class="text-center mb-4" style="font-size: 2rem;">📦  events Data</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🔹 الكروت الإحصائية --}}
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h5 class="text-primary"> Total events</h5>
                    <h2>{{ $totalevents }}</h2>
                </div>
            </div>
        </div>

        
    </div>

    {{-- 🔹 جدول الطلبات --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0">
            <h4 class="mb-0 text-muted">🧾  events List</h4>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>👤  User Nmae</th>
                        <th>  Name</th>
                        <th>  Date</th>
                        <th> Number Of Guests</th>
                        <th>⚙️ Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $event->user->name }}</td>
                            <td>{{ $event->name }}</td>
                            <td>{{ $event->date }}</td>
                            <td>{{ $event->number_of_guests }} </td>
                            <td>
                                <a href="{{ route('event.show' , $event->id) }}" class="btn btn-outline-primary btn-sm">
                                    عرض التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-3">🚫 لا توجد مناسبات  حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
