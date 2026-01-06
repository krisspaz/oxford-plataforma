<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PackageControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetPackages()
    {
        $this->client->request('GET', '/api/packages');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
