<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FamilyControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetFamilies()
    {
        $this->client->request('GET', '/api/families');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
