<?php

namespace App\Tests\Service;

use App\Service\AiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AiServiceTest extends TestCase
{
    public function testAskSendsCorrectHeaders()
    {
        $client = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('toArray')->willReturn(['response' => 'ok']);

        $client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://ai:8000/ask',
                $this->callback(function ($options) {
                    return $options['headers']['X-INTERNAL-KEY'] === 'secret'
                        && $options['json'] === ['prompt' => 'hello'];
                })
            )
            ->willReturn($response);

        $service = new AiService($client, 'http://ai:8000', 'secret');
        $result = $service->ask(['prompt' => 'hello']);

        $this->assertEquals(['response' => 'ok'], $result);
    }
}
