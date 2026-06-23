<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('user_edit');
    }

    public function rules(): array
    {
        $role = $this->input('role');

        $teamRoles = ['Leader', 'Co Leader', 'Stack Lead', 'Member', 'Probation'];
        $noOrgRoles = ['GM', 'AGM', 'Admin'];

        $rules = [
            'id' => 'required|exists:users,id',
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users', 'employee_id')->ignore($this->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->id)],
            'official_email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'official_email')->ignore($this->id)],
            'whatsapp' => ['required', 'string', 'max:20', Rule::unique('users', 'whatsapp')->ignore($this->id)],
            'phone' => ['nullable', 'string', Rule::unique('users', 'phone')->ignore($this->id), 'regex:/^[0-9]{10,15}$/'],
            'role' => 'required|exists:roles,name',
            'team_id' => 'nullable|exists:teams,id',
            'community_id' => 'nullable|exists:communities,id',
            'stack_id' => 'nullable|exists:stacks,id',
            'joining_date' => 'required|date',
            'probation_end_date' => 'required|date|after_or_equal:joining_date',
            'reporting_to' => ['nullable', 'exists:users,id', 'not_in:' . $this->id],
            'password' => 'required',
            'newpassword' => 'nullable|min:6|confirmed',
        ];

        if (in_array($role, $teamRoles, true)) {
            $rules['team_id'] = 'required|exists:teams,id';
            $rules['stack_id'] = 'required|exists:stacks,id';
            $rules['community_id'] = 'nullable';
        } elseif ($role === 'Operation Manager') {
            $rules['community_id'] = 'required|exists:communities,id';
            $rules['team_id'] = 'prohibited';
        } elseif (in_array($role, $noOrgRoles, true)) {
            $rules['community_id'] = 'prohibited';
            $rules['team_id'] = 'prohibited';
        }

        // Joining and probation dates are required for all roles per business rules

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('reporting_to')) {
                $manager = User::find($this->input('reporting_to'));
                if (!$manager || !$manager->hasRole('Operation Manager')) {
                    $validator->errors()->add('reporting_to', 'Reporting manager must be an Operation Manager.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'The employee ID field is required.',
            'employee_id.unique' => 'The employee ID has already been taken.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'This username is already taken.',
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'official_email.email' => 'Please enter a valid official email address.',
            'official_email.unique' => 'This official email is already registered.',
            'whatsapp.required' => 'The whatsapp number field is required.',
            'whatsapp.unique' => 'This whatsapp number is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'phone.regex' => 'The phone number must be 10-15 digits long.',
            'role.required' => 'Please select a role.',
            'role.exists' => 'Selected role is invalid.',
            'team_id.required' => 'Please select a team for this role.',
            'team_id.exists' => 'Selected team is invalid.',
            'team_id.prohibited' => 'Team selection is not allowed for this role.',
            'community_id.required' => 'Please select a community for this role.',
            'community_id.exists' => 'Selected community is invalid.',
            'community_id.prohibited' => 'Community selection is not allowed for this role.',
            'stack_id.required' => 'Please select a stack for this role.',
            'stack_id.exists' => 'Selected stack is invalid.',
            'joining_date.required' => 'Joining date is required for this role.',
            'joining_date.date' => 'Joining date must be a valid date.',
            'probation_end_date.required' => 'End of probation date is required for this role.',
            'probation_end_date.date' => 'End of probation date must be a valid date.',
            'probation_end_date.after_or_equal' => 'End of probation date must be after or equal to joining date.',
            'reporting_to.not_in' => 'A user cannot report to themselves.',
            'password.required' => 'The password you entered does not match your current password.',
            'newpassword.min' => 'Password must be at least 6 characters.',
            'newpassword.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
