<?php

namespace App\Http\Requests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'verify_code_email' => array_filter([
                config('settings.update_type') == 'verify_code' ? 'required' : null,
                'string'
            ]),
            'new_email' => array_filter([
                'nullable',
                'email',
                !config('settings.duplicate_email', 1) ? Rule::unique('users', 'email')->ignore($this->user()->id) : null
            ]),

            'email' => array_filter([
                config('settings.update_type') !== 'verify_code' ? 'required' : null,
                'string',
                'lowercase',
                'email',
                'max:255',
                !config('settings.duplicate_email', 1) ? Rule::unique(User::class)->ignore($this->user()->id) : null,

                function ($attribute, $value, $fail) {
                    if (config('global.server.version') === 'vSRO' && !config('settings.duplicate_email', 1)) {
                        $exists = $this->user()->tbUser()->where('Email', $value)->where('JID', '!=', $this->user()->jid)->exists();
                        if ($exists) {
                            $fail('The email has already been taken in another account.');
                        }
                    }
                },
            ]),
        ];
    }
}
