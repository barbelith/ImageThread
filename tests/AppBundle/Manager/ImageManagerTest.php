<?php


use AppBundle\Manager\ImageManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class ImageManagerTest extends TestCase
{
    /** @var  ImageManager */
    protected $imageManager;

    protected function setUp()
    {
        parent::setUp();
        
        $this->imageManager = new ImageManager('/image_thread_images_test_'.mt_rand(), sys_get_temp_dir());
    }

    protected function tearDown()
    {
        parent::tearDown();

        @rmdir($this->imageManager->getImagesPath());
    }

    /**
     * @dataProvider dataProviderImagesPath
     */
    public function testImagesPath($imagesDir, $webDir, $expected)
    {
        $imageManager = new ImageManager($imagesDir, $webDir);
        $this->assertEquals($expected, $imageManager->getImagesPath());
    }

    public function dataProviderImagesPath()
    {
        return array(
            array('/home/user/web/', '/images', '/home/user/web/images'),
            array('/home/user/web', '/images', '/home/user/web/images'),
            array('/home/user/web', 'images', '/home/user/web/images'),
        );
    }

    public function testSaveImageOnDisk()
    {
        $file = new File(__DIR__.'/../Util/fixtures/image.png');

        $this->imageManager->saveImageOnDisk($file, 'test.png');

        $this->assertTrue(is_file($this->imageManager->getImagesPath().DIRECTORY_SEPARATOR.'test.png'));
    }

}