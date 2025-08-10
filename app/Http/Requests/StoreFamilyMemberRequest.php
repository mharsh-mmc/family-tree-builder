<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can add authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'relation' => 'nullable|string|max:255',
            'dob' => 'nullable|date|before_or_equal:today',
            'is_alive' => 'boolean',
            'dod' => 'nullable|date|after:dob',
            'biodata' => 'nullable|string|max:1000',
            'profile_pic' => 'nullable|string|max:1000',
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The family member name is required.',
            'name.max' => 'The family member name cannot exceed 255 characters.',
            'dob.before_or_equal' => 'The date of birth cannot be in the future.',
            'dod.after' => 'The date of death must be after the date of birth.',
            'biodata.max' => 'The biography cannot exceed 1000 characters.',
            'profile_pic.max' => 'The profile picture URL cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic
            if ($this->input('is_alive') === false && empty($this->input('dod'))) {
                $validator->errors()->add('dod', 'Date of death is required when marking someone as deceased.');
            }
            
            if ($this->input('is_alive') === true && !empty($this->input('dod'))) {
                $validator->errors()->add('dod', 'Date of death should not be set for living members.');
            }
        });
    }
}