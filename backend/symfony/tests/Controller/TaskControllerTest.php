<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetTasks()
    {
        $this->client->request('GET', '/api/tasks');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
