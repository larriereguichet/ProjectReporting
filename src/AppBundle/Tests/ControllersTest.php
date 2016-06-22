<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllersTest extends WebTestCase
{
    public function testProjectController()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/project/list');

        $this->assertContains('Redirecting to /login', $crawler->text());
    }
}
