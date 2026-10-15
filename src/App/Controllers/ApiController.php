<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\FormRequest\Contracts\FormRequestFactoryInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\Http\Protocol\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ApiController
{
    public function __construct(
        private readonly ResponseFactoryInterface $response,
        private readonly FormRequestFactoryInterface $formRequests,
    ) {}

    public function ping(): ResponseInterface
    {
        return $this->response->jsonSuccess(['message' => 'pong']);
    }

    /**
     * Validates JSON body via FormRequestFactory (same ContactFormRequest as web step 3).
     */
    public function contact(ServerRequestInterface $request): ResponseInterface
    {
        $form = $this->formRequests->make(ContactFormRequest::class, $request);

        if (!$form->validate()) {
            return $this->response->jsonError(
                'Validation failed',
                HttpStatusCode::UNPROCESSABLE_ENTITY,
                $form->errors(),
            );
        }

        return $this->response->jsonSuccess([
            'validated' => $form->validated(),
        ]);
    }
}
