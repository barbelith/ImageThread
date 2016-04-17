<?php


namespace tests\AppBundle\Repository;


use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use AppBundle\Test\KernelTestCase;

class PostRepositoryTest extends KernelTestCase
{
    /** @var  PostRepository */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->getEntityManager()->getRepository(
          'AppBundle:Post'
        );
    }

    /**
     * @param $nbToCreate
     * @param $lastItem
     * @param $offset
     * @param $expectedCount
     * @internal param $expected
     * @dataProvider dataProviderGetPostsWithOffset
     */
    public function testGetPostsWithOffset($nbToCreate, $lastItem, $offset, $expectedCount)
    {
        for ($i = 1; $i <= $nbToCreate; $i++) {
            $post = new Post();
            $post->setId($i);
            $post->setImage($i.'.png');
            $this->getEntityManager()->persist($post);
        }

        $this->getEntityManager()->flush();

        $posts = $this->repository->getPostsWithOffset($lastItem, $offset);

        $this->assertEquals($expectedCount, count($posts));
    }

    public function dataProviderGetPostsWithOffset()
    {
        return array(
          array(10, 0, 10, 10),
          array(10, 1, 10, 0),
          array(11, 2, 10, 1),
          array(0, 0, 10, 0),
          array(0, 1, 10, 0),
          array(10, 0, 5, 5),
        );
    }
}