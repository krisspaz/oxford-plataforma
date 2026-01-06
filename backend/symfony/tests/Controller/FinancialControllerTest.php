<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FinancialControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetPayments()
    {
        $this->client->request('GET', '/api/payments');
        $this->assertContains($this->client->getResponse()->getStatusCode(), [200, 401]);
    }

    public function testRecordPayment()
    {
        $this->client->request('POST', '/api/payments', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'studentId' => 1,
            'amount' => 500.00,
            'concept' => 'Colegiatura Enero'
        ]));
        $this->assertContains($this->client->getResponse()->getStatusCode(), [201, 401, 400]);
    }
}
