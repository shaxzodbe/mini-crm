@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('manager.tickets.index') }}" class="btn btn-secondary mb-4">← К списку заявок</a>

        <h2>Заявка #{{ $ticket->id }} - {{ $ticket->subject }}</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Детали заявки
                    </div>
                    <div class="card-body">
                        <p><strong>Тема:</strong> {{ $ticket->subject }}</p>
                        <p><strong>Текст:</strong></p>
                        <div class="alert alert-light">{{ $ticket->text }}</div>
                        <p><strong>Дата создания:</strong> {{ $ticket->created_at->format('Y-m-d H:i:s') }}</p>
                        @if ($ticket->manager_response_at)
                            <p><strong>Дата ответа менеджера:</strong> {{ $ticket->manager_response_at->format('Y-m-d H:i:s') }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        Прикрепленные файлы ({{ $ticket->getMedia('attachments')->count() }})
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse ($ticket->getMedia('attachments') as $media)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $media->file_name }} ({{ number_format($media->size / 1024 / 1024, 2) }} MB)
                                <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-success" download>
                                    Скачать
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Файлы отсутствуют.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        Информация о клиенте
                    </div>
                    <div class="card-body">
                        <p><strong>Имя:</strong> {{ $ticket->customer->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> <a href="mailto:{{ $ticket->customer->email }}">{{ $ticket->customer->email }}</a></p>
                        <p><strong>Телефон:</strong> {{ $ticket->customer->phone }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-warning">
                        Управление статусом
                    </div>
                    <div class="card-body">
                        <p>Текущий статус:
                            <span class="badge bg-{{ $ticket->status === 'new' ? 'danger' : ($ticket->status === 'in_work' ? 'warning' : 'success') }}">
                            {{ __('ticket_statuses.' . $ticket->status) }}
                        </span>
                        </p>

                        <form method="POST" action="{{ route('manager.tickets.update_status', $ticket) }}">
                            @csrf
                            @method('PATCH')

                            <div class="form-group mb-3">
                                <label for="status">Изменить на:</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" @selected($ticket->status == $status->value)>
                                            {{ __('ticket_statuses.' . $status->value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Обновить статус</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
