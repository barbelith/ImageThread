<?php


namespace tests\AppBundle\Exporter;

use AppBundle\Entity\Post;
use AppBundle\Export\CsvExporter;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

class CsvExporterTest extends TestCase
{
    public function testPrepareWithNoItems()
    {
        $exporter = new CsvExporter([]);
        
        $export = $exporter->prepare()->getData();

        $this->assertCount(1, $export);

        $this->assertEquals([['Title', 'Filename']], $export);
    }

    public function testPrepareWithItems()
    {
        $post1 = new Post();
        $post1->setImage('image1.png');
        $post1->setTitle('Image 1');

        $post2 = new Post();
        $post2->setImage('image2.png');
        $post2->setTitle('Image 2');

        $exporter = new CsvExporter([$post1, $post2]);

        $export = $exporter->prepare()->getData();

        $this->assertCount(3, $export);
        $this->assertEquals([$post1->getTitle(), $post1->getImage()], $export[1]);
        $this->assertEquals([$post2->getTitle(), $post2->getImage()], $export[2]);
    }

    public function testPrepareWithNoTitle()
    {
        $post = new Post();
        $post->setImage('image1.png');

        $exporter = new CsvExporter([$post]);

        $export = $exporter->prepare()->getData();

        $this->assertCount(2, $export);
        $this->assertEquals([null, 'image1.png'], $export[1]);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSaveWithoutPrepare()
    {
        $exporter = new CsvExporter([]);
        $exporter->save(sys_get_temp_dir().'image_thread_export.csv');
    }

    public function testSaveCreatesFile()
    {
        $exporter = new CsvExporter([]);
        $exporter->prepare();
        $path = sys_get_temp_dir().'image_thread_export.csv';

        $exporter->save($path);

        $this->assertFileExists($path);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSaveInvalidPath()
    {
        $exporter = new CsvExporter([]);
        $exporter->prepare();
        $path = 'aaa/bbb.csv';

        $exporter->save($path);

        $this->assertFileExists($path);
    }
}