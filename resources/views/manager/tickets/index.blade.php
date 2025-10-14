@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Список заявок ({{ $tickets->total() }})</h2>

        @include('manager.tickets.partials.filters')

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Тема</th>
                <th>Клиент</th>
                <th>Статус</th>
                <th>Дата создания</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ Str::limit($ticket->subject, 50) }}</td>
                    <td>
                        {{ $ticket->customer->name ?? 'N/A' }} <br>
                        <small>{{ $ticket->customer->email }}</small>
                    </td>
                    <td>
                        <span class="badge bg-{{ $ticket->status === 'new' ? 'danger' : ($ticket->status === 'in_work' ? 'warning' : 'success') }}">
                            {{ __('ticket_statuses.' . $ticket->status) }}
                        </span>
                    </td>
                    <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('manager.tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                            Просмотр
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Заявок по вашему запросу не найдено.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $tickets->withQueryString()->links() }}
    </div>
@endsection
