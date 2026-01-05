<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public function testGetOverduePaymentsConfig(): void
    {
        $client = static::createClient();
        
        // Check if endpoint exists (requires auth in real scenario, so we expect 401 or 200)
        // Here we just verify route availability
        $client->request('GET', '/api/payments/overdue');
        
        $statusCode = $client->getResponse()->getStatusCode();
        
        // Assert that we get a response (401 Unauthorized is expected if security is on, 
        // 200 if public or auth mocked. This verifies the controller route is registered).
        $this->assertTrue(in_array($statusCode, [200, 401]));
    }
}
