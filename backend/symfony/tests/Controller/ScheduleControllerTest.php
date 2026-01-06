<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetSchedules()
    {
        $this->client->request('GET', '/api/schedules');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
