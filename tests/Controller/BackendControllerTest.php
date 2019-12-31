<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class BackendControllerTest
 */
class BackendControllerTest extends WebTestCase
{
    /**
     * Test backend pages
     */
    public function testLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
