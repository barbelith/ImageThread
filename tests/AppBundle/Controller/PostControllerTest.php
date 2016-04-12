<?php


namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostControllerTest extends WebTestCase
{
    public function testFormLoaded()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/post/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull($crawler->selectButton('post[save]')->form());
    }

    public function testUploadImage()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $crawler = $client->request('GET', '/post/create');

        $form = $crawler->selectButton('post[save]')->form();

        $client->submit($form, array(
            'post[image_upload]' => new UploadedFile(__DIR__.'/../Util/fixtures/image.png', 'image.png')
        ));

        $client->followRedirect();

        $this->assertEquals(1, $client->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Post')->createQueryBuilder('u')->select('count(u.id)')->getQuery()->getSingleScalarResult());
    }

    public function testUploadImageAndTitle()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $crawler = $client->request('GET', '/post/create');

        $form = $crawler->selectButton('post[save]')->form();

        $client->submit($form, array(
            'post[title]' => 'Image title',
            'post[image_upload]' => new UploadedFile(__DIR__.'/../Util/fixtures/image.png', 'image.png')
        ));

        $client->followRedirect();

        $postRepository = $client->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Post');
        $this->assertEquals(1, $postRepository->createQueryBuilder('u')->select('count(u.id)')->getQuery()->getSingleScalarResult());

        /** @var Post $post */
        $post = $postRepository->findOneBy(array());

        $this->assertEquals('Image title', $post->getTitle());
    }

    public function testUploadInvalidFile()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $crawler = $client->request('GET', '/post/create');

        $form = $crawler->selectButton('post[save]')->form();

        $client->submit(
          $form,
          array(
            'post[image_upload]' => new UploadedFile(__DIR__.'/../Util/fixtures/text_file.txt', 'text_file.txt')
          )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $postRepository = $client->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Post');
        $this->assertEquals(
          0,
          $postRepository->createQueryBuilder('u')->select('count(u.id)')->getQuery()->getSingleScalarResult()
        );
    }
}