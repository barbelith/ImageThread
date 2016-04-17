<?php


namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use AppBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostControllerTest extends WebTestCase
{
    public function testFormLoaded()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

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

        $this->assertEquals(1, $client->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Post')->count());
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

        /** @var PostRepository $postRepository */
        $postRepository = $client->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Post');
        $this->assertEquals(1, $postRepository->count());

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
          $postRepository->count()
        );
    }

    public function testListLoaded()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $crawler = $client->request('GET', '/post/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull($crawler->filter('ul.posts')->count());
    }

    public function testListWithPosts()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $em = $this->getDoctrine($client->getContainer());

        for ($i = 1; $i <= 5; $i++) {
            $post = new Post();
            $post->setTitle('Post #'.$i);
            $post->setImage($i.'.jpg');
            $em->persist($post);
        }

        $em->flush();

        $crawler = $client->request('GET', '/post/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull($crawler->filter('ul.posts')->count());
        $this->assertEquals(5, $crawler->filter('ul.posts li.post')->count());
    }

    public function testExport()
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $crawler = $client->request('GET', '/post/export');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotNull($crawler->selectButton('post_export[export]')->form());
    }

    /**
     * @param $expectedResponseClass
     * @param $exportType
     * @param $withImages
     * @dataProvider dataProviderExportDownloadsFile
     */
    public function testExportDownloadsFile($expectedResponseClass, $exportType, $withImages)
    {
        $client = static::createClient();

        $this->generateSchema($client->getContainer());

        $em = $this->getDoctrine($client->getContainer());

        for ($i = 1; $i <= 5; $i++) {
            $post = new Post();
            $post->setTitle('Post #'.$i);
            $post->setImage($i.'.jpg');
            $em->persist($post);
        }

        $em->flush();

        $crawler = $client->request('GET', '/post/export');

        $form = $crawler->selectButton('post_export[export]')->form();

        ob_start();

        $parameters = array(
          'post_export[export_type]' => $exportType
        );

        if ($withImages) {
            $parameters['post_export[export_include_images]'] = 1;
        }

        $client->submit(
          $form,
          $parameters
        );

        ob_end_clean();

        $this->assertInstanceOf($expectedResponseClass, $client->getResponse());
    }

    public function dataProviderExportDownloadsFile()
    {
        $streamedResponse = 'Symfony\Component\HttpFoundation\StreamedResponse';
        $binaryResponse = 'Symfony\Component\HttpFoundation\BinaryFileResponse';

        return array(
            array($streamedResponse, 'csv', false),
            array($streamedResponse, 'excel', false),
            array($binaryResponse, 'csv', true),
            array($binaryResponse, 'excel', true),
        );
    }

}