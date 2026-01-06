<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReportControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGenerateReport()
    {
        $this->client->request('POST', '/api/reports/generate', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'type' => 'grades',
            'year' => 2026
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }
}
