<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class AddProjectMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'exists:users,id'],
            'role' => ['sometimes', 'string', 'in:owner,admin,member,viewer'],
        ];
    }
}
