<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AiService
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'AI_SERVICE_URL')]
        private string $aiUrl,
        #[Autowire(env: 'AI_INTERNAL_KEY')]
        private string $internalKey
    ) {}

    public function ask(array $payload)
    {
        return $this->client->request('POST', $this->aiUrl.'/ask', [
            'headers' => [
                'X-INTERNAL-KEY' => $this->internalKey
            ],
            'json' => $payload
        ])->toArray();
    }
}
