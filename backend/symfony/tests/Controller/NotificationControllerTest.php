<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testSendNotification()
    {
        $this->client->request('POST', '/api/notifications/send', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'userId' => 1,
            'message' => 'Hello World'
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
