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
