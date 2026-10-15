<?php declare(strict_types=1);

namespace Concept\App\Http\Requests;

use Concept\Extensions\FormRequest\Requests\FormRequest;

/**
 * Demo FormRequest for the contact form (recipe step 3).
 *
 * @extends FormRequest<never>
 */
final class ContactFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'message' => 'required|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name:required' => 'Please enter your name.',
            'name:min' => 'Name must be at least 2 characters.',
            'email:required' => 'Please enter your email.',
            'email:email' => 'Invalid email.',
            'message:required' => 'Please enter a message.',
            'message:min' => 'Message must be at least 10 characters.',
        ];
    }
}
