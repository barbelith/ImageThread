<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Image Thread App, upload your images here!!!', $crawler->filter('title')->text());
    }

    public function testIndexAddsView()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $client->request('GET', '/');

        $this->assertEquals(
          1,
          $client->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Statistic')->getNumberViews()
        );
    }
}
