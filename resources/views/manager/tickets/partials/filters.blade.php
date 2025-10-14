<form method="GET" action="{{ route('manager.tickets.index') }}" class="card p-3 mb-4">
    <h5>Фильтрация заявок</h5>
    <div class="row">

        <div class="col-md-3 mb-3">
            <label for="status">Статус</label>
            <select name="status" id="status" class="form-control">
                <option value="">Все статусы</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(isset($filters['status']) && $filters['status'] == $status->value)>
                        {{ __('ticket_statuses.' . $status->value) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label for="email">Email клиента</label>
            <input type="text" name="email" id="email" class="form-control" value="{{ $filters['email'] ?? '' }}">
        </div>

        <div class="col-md-3 mb-3">
            <label for="phone">Телефон клиента</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $filters['phone'] ?? '' }}">
        </div>

        <div class="col-md-3 mb-3">
            <label for="date_from">Дата от</label>
            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
        </div>

    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary me-2">Применить фильтр</button>
        <a href="{{ route('manager.tickets.index') }}" class="btn btn-secondary">Сбросить</a>
    </div>
</form>
