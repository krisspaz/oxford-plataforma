<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BimesterControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetBimesters()
    {
        $this->client->request('GET', '/api/bimesters');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
