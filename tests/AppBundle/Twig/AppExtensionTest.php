<?php


namespace tests\AppBundle\Twig;


use AppBundle\Twig\AppExtension;
use Mockery as m;

class AppExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPostsCount()
    {
        $numPosts = 10;

        $imageManager = m::mock('AppBundle\Manager\ImageManager');

        $repository = m::mock('AppBundle\Repository\Post');
        $repository->shouldReceive('count')->once()->andReturn($numPosts);

        $entityManager = m::mock('Doctrine\ORM\EntityManager');
        $entityManager->shouldReceive('getRepository')->with('AppBundle:Post')->andReturn($repository)->once();


        $extension = new AppExtension($imageManager, $entityManager);

        $this->assertEquals($numPosts, $extension->getPostsCount());
    }

    public function testGetViewsCount()
    {
        $numViews = 50;

        $imageManager = m::mock('AppBundle\Manager\ImageManager');

        $repository = m::mock('AppBundle\Repository\Statistic');
        $repository->shouldReceive('getNumberViews')->once()->andReturn($numViews);

        $entityManager = m::mock('Doctrine\ORM\EntityManager');
        $entityManager->shouldReceive('getRepository')->with('AppBundle:Statistic')->andReturn($repository)->once();


        $extension = new AppExtension($imageManager, $entityManager);

        $this->assertEquals($numViews, $extension->getViewsCount());
    }

    public function testGetImageUrl()
    {
        $url = 'http://www.image.com/image.jpg';

        $imageManager = m::mock('AppBundle\Manager\ImageManager');
        $imageManager->shouldReceive('getImageUrl')->andReturn($url);

        $entityManager = m::mock('Doctrine\ORM\EntityManager');


        $extension = new AppExtension($imageManager, $entityManager);

        $this->assertEquals($url, $extension->getImageUrl('image.jpg'));
    }
}