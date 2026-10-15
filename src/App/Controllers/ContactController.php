<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\DatabaseEloquent\Contracts\DatabaseInterface;
use Concept\Extensions\View\Contracts\ViewResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ContactController
{
    public function __construct(
        private readonly ViewResponseFactoryInterface $view,
        private readonly DatabaseInterface $database,
    ) {}

    public function show(): ResponseInterface
    {
        return $this->view->create('contact', [
            'success' => false,
            'validated' => null,
        ]);
    }

    /**
     * Type-hint FormRequest: resolver validates or throws ValidationException
     * (HandleValidationExceptionMiddleware → flash + redirect).
     * csrf / flashes / errors / old come from ShareViewDataMiddleware → VIEW_PAYLOAD.
     */
    public function submit(ContactFormRequest $form): ResponseInterface
    {
        $validated = $form->validated();

        $this->database->capsule()->table('contact_submissions')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->view->create('contact', [
            'success' => true,
            'validated' => $validated,
        ]);
    }
}
