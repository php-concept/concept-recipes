<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Dto\ContactDto;
use Concept\App\Http\Requests\ContactFormRequest;
use Concept\Extensions\DatabaseEloquent\Contracts\DatabaseInterface;
use Concept\Extensions\Http\Protocol\HttpStatusCode;
use Concept\Extensions\LoggerMonolog\Contracts\LoggerInterface;
use Concept\Extensions\View\Contracts\ViewResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ContactController
{
    public function __construct(
        private readonly ViewResponseFactoryInterface $view,
        private readonly DatabaseInterface $database,
        private readonly LoggerInterface $logger,
    ) {}

    public function show(): ResponseInterface
    {
        return $this->view->create('contact', [
            'success' => false,
            'validated' => null,
        ]);
    }

    /**
     * Type-hint FormRequest: resolver validates or throws ValidationException.
     * After casting: toDto() → ContactDto (requires addCasting()).
     */
    public function submit(ContactFormRequest $form): ResponseInterface
    {
        $dto = $form->toDto();
        if (!$dto instanceof ContactDto) {
            throw new \RuntimeException('ContactDto casting failed — is addCasting() enabled?');
        }

        $this->database->capsule()->table('contact_submissions')->insert($dto->toArray());

        $this->logger->info('Contact form submitted', [
            'email' => $dto->email,
        ]);

        return $this->view->create('contact', [
            'success' => true,
            'validated' => $dto->toArray(),
        ]);
    }

    /**
     * Show one submission by id ({id} → int via withTypedRouteParameters + Casting).
     */
    public function showById(int $id): ResponseInterface
    {
        $row = $this->database->capsule()->table('contact_submissions')->where('id', $id)->first();

        if ($row === null) {
            return $this->view->create('contact-show', [
                'found' => false,
                'id' => $id,
                'submission' => null,
            ], HttpStatusCode::NOT_FOUND);
        }

        return $this->view->create('contact-show', [
            'found' => true,
            'id' => $id,
            'submission' => (array) $row,
        ]);
    }
}
