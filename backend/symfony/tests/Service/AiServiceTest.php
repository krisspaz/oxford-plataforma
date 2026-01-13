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
        $cache = $this->createMock(\Symfony\Contracts\Cache\CacheInterface::class);
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('toArray')->willReturn(['response' => 'ok']);

        $item = $this->createMock(\Symfony\Contracts\Cache\ItemInterface::class);
        $cache->method('get')->willReturnCallback(function($key, $callback) use ($item) {
             return $callback($item);
        });

        $client->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://ai:8000/process-command',
                $this->callback(function ($options) {
                    return $options['headers']['X-INTERNAL-KEY'] === 'secret'
                        && $options['json'] === ['prompt' => 'hello'];
                })
            )
            ->willReturn($response);

        $service = new AiService($client, $cache, $logger, 'http://ai:8000', 'secret');
        $result = $service->ask(['prompt' => 'hello']);

        $this->assertEquals(['response' => 'ok'], $result);
    }
}
