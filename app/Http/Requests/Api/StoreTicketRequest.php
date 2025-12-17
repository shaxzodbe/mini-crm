<?php

namespace App\Http\Requests\Api;

use App\Repositories\TicketRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
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
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'Вы можете отправить только одну заявку в течение 24 часов. Пожалуйста, подождите.',
                    ], 429)
                );
            }
        });
    }
}
