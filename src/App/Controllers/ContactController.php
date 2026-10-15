<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\Csrf\Contracts\CsrfTokenManagerInterface;
use Concept\Extensions\DatabaseEloquent\Contracts\DatabaseInterface;
use Concept\Extensions\FormRequest\Contracts\FormRequestFactoryInterface;
use Concept\Extensions\Http\Protocol\HttpStatusCode;
use Concept\Extensions\SessionSymfony\Contracts\FlashBagInterface;
use Concept\Extensions\View\Contracts\ViewResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ContactController
{
    public function __construct(
        private readonly ViewResponseFactoryInterface $view,
        private readonly FormRequestFactoryInterface $formRequests,
        private readonly CsrfTokenManagerInterface $csrf,
        private readonly FlashBagInterface $flashBag,
        private readonly DatabaseInterface $database,
    ) {}

    public function show(): ResponseInterface
    {
        return $this->view->create('contact', $this->formViewData());
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
                ...$this->formViewData(),
                'errors' => $form->errors(),
                'old' => $form->all(),
                'success' => false,
                'validated' => null,
            ], HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        $validated = $form->validated();

        $this->database->capsule()->table('contact_submissions')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->view->create('contact', [
            ...$this->formViewData(),
            'errors' => [],
            'old' => [],
            'success' => true,
            'validated' => $validated,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formViewData(): array
    {
        return [
            'csrf_token' => $this->csrf->getToken(),
            'flashes' => $this->flashBag->all(),
            'errors' => [],
            'old' => [],
            'success' => false,
            'validated' => null,
        ];
    }
}
