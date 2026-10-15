<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\DatabaseEloquent\Contracts\DatabaseInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiController
{
    public function __construct(
        private readonly ResponseFactoryInterface $response,
        private readonly DatabaseInterface $database,
    ) {}

    public function ping(): ResponseInterface
    {
        return $this->response->jsonSuccess(['message' => 'pong']);
    }

    public function stats(): ResponseInterface
    {
        $submissions = $this->database->capsule()->table('contact_submissions');

        return $this->response->jsonSuccess([
            'connection' => 'ok',
            'contact_submissions_total' => $submissions->count(),
        ]);
    }

    /**
     * Type-hint FormRequest: resolver validates or throws ValidationException
     * (HandleJsonValidationExceptionMiddleware → 422 JSON).
     */
    public function contact(ContactFormRequest $form): ResponseInterface
    {
        $validated = $form->validated();

        $this->database->capsule()->table('contact_submissions')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->jsonSuccess([
            'validated' => $validated,
            'saved' => true,
        ]);
    }
}
