<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\FormRequest\Contracts\FormRequestFactoryInterface;
use Concept\Extensions\Http\Protocol\HttpStatusCode;
use Concept\Extensions\View\Contracts\ViewResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ContactController
{
    public function __construct(
        private readonly ViewResponseFactoryInterface $view,
        private readonly FormRequestFactoryInterface $formRequests,
    ) {}

    public function show(): ResponseInterface
    {
        return $this->view->create('contact', [
            'errors' => [],
            'old' => [],
            'success' => false,
            'validated' => null,
        ]);
    }

    /**
     * Validates via FormRequestFactory and re-renders the form on failure.
     * (Type-hint ContactFormRequest throws ValidationException — needs ErrorHandling later.)
     */
    public function submit(ServerRequestInterface $request): ResponseInterface
    {
        $form = $this->formRequests->make(ContactFormRequest::class, $request);

        if (!$form->validate()) {
            return $this->view->create('contact', [
                'errors' => $form->errors(),
                'old' => $form->all(),
                'success' => false,
                'validated' => null,
            ], HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        return $this->view->create('contact', [
            'errors' => [],
            'old' => [],
            'success' => true,
            'validated' => $form->validated(),
        ]);
    }
}
