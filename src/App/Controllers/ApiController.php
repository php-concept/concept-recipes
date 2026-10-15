<?php declare(strict_types=1);

namespace Concept\App\Controllers;

use Concept\App\Dto\ContactDto;
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
     * (HandleJsonValidationExceptionMiddleware → 422 JSON). After casting: toDto().
     */
    public function contact(ContactFormRequest $form): ResponseInterface
    {
        $dto = $form->toDto();
        if (!$dto instanceof ContactDto) {
            throw new \RuntimeException('ContactDto casting failed — is CastingBrick enabled?');
        }

        $this->database->capsule()->table('contact_submissions')->insert($dto->toArray());

        return $this->response->jsonSuccess([
            'validated' => $dto->toArray(),
            'saved' => true,
        ]);
    }
}
