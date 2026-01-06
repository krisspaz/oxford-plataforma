<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContractControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetContract()
    {
        $this->client->request('GET', '/api/contracts/1');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401, 404]);
    }
}
