<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
        ];

        // Student-specific fields
        if ($this->user()->role === 'student') {
            $rules['student_id'] = ['nullable', 'string', 'max:50', Rule::unique(User::class)->ignore($this->user()->id)];
            $rules['faculty'] = ['nullable', 'string', 'max:255'];
            $rules['course'] = ['nullable', 'string', 'max:255'];
        }

        // Staff email for community users
        if ($this->user()->isCommunity() && !$this->user()->isVerifiedStaff()) {
            $rules['staff_email'] = [
                'nullable', 
                'email', 
                'ends_with:@upsi.edu.my',
                Rule::unique(User::class)->ignore($this->user()->id)
            ];
        }

        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'staff_email.ends_with' => 'Staff email must be a valid UPSI email address (@upsi.edu.my)',
            'student_id.unique' => 'This student ID is already registered.',
            'bio.max' => 'Bio must not exceed 500 characters.',
        ];
    }
}
