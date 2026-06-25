<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaderMemberStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user instanceof User && $user->hasRole('Leader');
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['nullable', 'string', 'max:255', 'unique:users,employee_id'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'unique:users,whatsapp'],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in(['Co Leader', 'Stack Lead', 'Member', 'Probation'])],
            'stack_id' => ['required', 'exists:stacks,id'],
            'joining_date' => ['required', 'date'],
            'probation_end_date' => ['required', 'date', 'after_or_equal:joining_date'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            /** @var User|null $leader */
            $leader = Auth::user();
            $role = $this->input('role');
            $teamId = $leader?->team_id;

            if (in_array($role, ['Co Leader', 'Stack Lead'], true)
                && User::hasConflictingTeamRole($role, $teamId, $this->input('stack_id'))) {
                $message = $role === 'Stack Lead'
                    ? 'This stack already has a Stack Lead in your team.'
                    : 'This team already has a Co Leader.';
                $validator->errors()->add('role', $message);
            }

            if ($role === 'Probation' && $this->filled('probation_end_date')
                && \Carbon\Carbon::parse($this->input('probation_end_date'))->lt(now()->startOfDay())) {
                $validator->errors()->add('probation_end_date', 'This probation end date has already passed, so the member is not in probation.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'The employee ID has already been taken.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'This username is already taken.',
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'whatsapp.unique' => 'This whatsapp number is already registered.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Please select a role.',
            'role.in' => 'Selected role is not allowed for Leader.',
            'stack_id.required' => 'Please select a stack.',
            'stack_id.exists' => 'Selected stack is invalid.',
            'joining_date.required' => 'Joining date is required.',
            'probation_end_date.required' => 'End of probation date is required.',
            'probation_end_date.after_or_equal' => 'End of probation date must be after or equal to joining date.',
            'phone.regex' => 'The phone number must be 10-15 digits long.',
        ];
    }
}
