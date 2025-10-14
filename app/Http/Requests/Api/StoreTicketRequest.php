<?php

namespace App\Http\Requests\Api;

use App\Repositories\TicketRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15', 'regex:/^\+\d{9,15}$/'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'mimes:jpg,png,pdf,doc,docx', 'max:2048'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Имя клиента.',
                'required' => false,
                'example' => 'Иван Петров',
            ],
            'phone' => [
                'description' => 'Номер телефона клиента в международном формате (например, +79001234567).',
                'required' => true,
                'example' => '+79001234567',
            ],
            'email' => [
                'description' => 'Адрес электронной почты клиента.',
                'required' => true,
                'example' => 'ivan@example.com',
            ],
            'subject' => [
                'description' => 'Тема заявки/запроса.',
                'required' => true,
                'example' => 'Проблема с авторизацией',
            ],
            'text' => [
                'description' => 'Полный текст сообщения/описания проблемы.',
                'required' => true,
                'example' => 'При попытке войти в систему возникает ошибка 500.',
            ],
            'files' => [
                'description' => 'Массив файлов для прикрепления (не более 5).',
                'required' => false,
                'example' => null, // Или пример с файлом, если возможно
            ],
            // Примечание: 'files.*' не требует отдельного bodyParameter, так как это часть 'files'
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $ticketRepository = app(TicketRepository::class);

        $validator->after(function ($validator) use ($ticketRepository) {

            $recentTicket = $ticketRepository->getRecentTicketByContact(
                $this->input('phone'),
                $this->input('email'),
                Carbon::now()->subDay()
            );

            if ($recentTicket) {
                $validator->errors()->add('phone', 'С вашего номера или email уже была отправлена заявка за последние 24 часа. Пожалуйста, подождите.');
                $validator->errors()->add('email', 'С вашего номера или email уже была отправлена заявка за последние 24 часа. Пожалуйста, подождите.');
            }
        });
    }
}
