<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Post;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $postId = $this->route('id');
        $post = Post::find($postId);
        
        if (!$post) {
            return false; 
        }
        
        return $this->user()->can('delete', $post);
    }
    
    public function rules(): array
    {
        return [
            'id' => [
                'required', 
                'integer', 
                'exists:posts,id' 
            ],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => '投稿IDは必須です。',
            'id.exists' => '指定された投稿IDは存在しません。',
        ];
    }
    
    public function validationData()
    {
        return array_merge($this->all(), [
            'id' => $this->route('id'),
        ]);
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