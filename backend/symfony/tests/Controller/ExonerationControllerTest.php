<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExonerationControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateExoneration()
    {
        $this->client->request('POST', '/api/exonerations', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'studentId' => 1,
            'reason' => 'Scholarship',
            'amount' => 100
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [201, 401]);
    }
}
