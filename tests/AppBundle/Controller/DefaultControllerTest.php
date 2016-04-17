<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\DefaultController;
use AppBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

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

    public function testViewsCountWithNoViews()
    {
        $client = static::createClient();

        $client->getContainer()->get('imagethread.cache')->deleteAll();
        $this->generateSchema($client->getContainer());

        $client->request('GET', '/view/count');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('ok', $response['status']);
        $this->assertEquals('Views: 0', $response['content']);
    }

    public function testCountWithPosts()
    {
        $client = static::createClient();

        $client->getContainer()->get('imagethread.cache')->deleteAll();
        $this->generateSchema($client->getContainer());

        $em = $this->getDoctrine($client->getContainer());
        $em->getRepository('AppBundle:Statistic')->addView();
        $em->flush();

        $client->request('GET', '/view/count');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('ok', $response['status']);
        $this->assertEquals('Views: 1', $response['content']);
    }

    public function testCountReturnsCacheValue()
    {
        $client = static::createClient();

        $client->getContainer()->get('imagethread.cache')->save(DefaultController::CACHE_KEY_VIEWS_COUNT, 20);

        $client->request('GET', '/view/count');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Views: 20', $response['content']);
    }

    public function testIndexUpdatesCount()
    {
        $client = static::createClient();

        $client->getContainer()->get('imagethread.cache')->deleteAll();
        $this->generateSchema($client->getContainer());

        $client->request('GET', '/view/count');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('ok', $response['status']);
        $this->assertEquals('Views: 0', $response['content']);

        $client->request('GET', '/');

        $client->request('GET', '/view/count');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Views: 1', $response['content']);
    }
}
