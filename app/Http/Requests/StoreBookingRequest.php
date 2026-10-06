<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('instagram')) {
            $this->merge(['instagram' => ltrim($this->input('instagram'), '@')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $timeslots = collect(Booking::timeslots())
            ->flatten()
            ->map(fn (Carbon $slot) => $slot->format('Y-m-d H:i'));

        return [
            'name' => ['required', 'string', 'max:255'],
            'timeslot' => ['required', 'string', Rule::in($timeslots), Rule::notIn(Booking::bookedTimeslots())],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:30', 'regex:/^[A-Za-z0-9._]+$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'timeslot.in' => 'Please choose one of the available timeslots.',
            'timeslot.not_in' => 'Sorry, that timeslot has just been booked. Please choose another.',
            'instagram.regex' => 'Instagram handles can only contain letters, numbers, full stops and underscores.',
        ];
    }
}
