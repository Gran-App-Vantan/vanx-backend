<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\PointRecoverySession;

class UseRecoveryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                'string',
                'exists:point_recovery_sessions,token',
                function ($attribute, $value, $fail) {
                    $session = PointRecoverySession::where('token', $value)->first();
                    
                    if ($session && $session->expires_at && $session->expires_at->isPast()) {
                        $fail('このポイント回復トークンは有効期限が切れています');
                    }
                },
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'point_recovery_token.required' => 'ポイント回復トークンが入力されていません',
            'point_recovery_token.string' => 'ポイント回復トークンは文字列で入力してください',
            'point_recovery_token.exists' => 'ポイント回復トークンが見つかりませんでした',
        ];
    }   
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'messages' => collect($validator->errors()->messages())
                    ->flatten()
                    ->toArray()
            ], 422)
        );
    }  
}
