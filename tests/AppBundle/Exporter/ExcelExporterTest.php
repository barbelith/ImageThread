<?php


namespace tests\AppBundle\Exporter;


use AppBundle\Entity\Post;
use AppBundle\Export\ExcelExporter;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class ExcelExporterTest extends TestCase
{
    public function testPrepareWithNoItems()
    {
        $exporter = new ExcelExporter([]);

        $excel = $exporter->prepare()->getExcel();

        $this->assertNotNull($excel);
        $this->assertEquals(1, $excel->getSheetCount());

        $this->assertEquals('Title', $excel->getActiveSheet()->getCell('A1')->getValue());
        $this->assertEquals('Filename', $excel->getActiveSheet()->getCell('B1')->getValue());
    }

    public function testPrepareWithItems()
    {
        $post1 = new Post();
        $post1->setImage('image1.png');
        $post1->setTitle('Image 1');

        $post2 = new Post();
        $post2->setImage('image2.png');
        $post2->setTitle('Image 2');

        $exporter = new ExcelExporter([$post1, $post2]);

        $excel = $exporter->prepare()->getExcel();

        $this->assertEquals($post1->getTitle(), $excel->getActiveSheet()->getCell('A2')->getValue());
        $this->assertEquals($post1->getImage(), $excel->getActiveSheet()->getCell('B2')->getValue());
        $this->assertEquals($post2->getTitle(), $excel->getActiveSheet()->getCell('A3')->getValue());
        $this->assertEquals($post2->getImage(), $excel->getActiveSheet()->getCell('B3')->getValue());
    }

    public function testPrepareWithNoTitle()
    {
        $post = new Post();
        $post->setImage('image1.png');

        $exporter = new ExcelExporter([$post]);

        $excel = $exporter->prepare()->getExcel();

        $this->assertEquals(null, $excel->getActiveSheet()->getCell('A2')->getValue());
        $this->assertEquals($post->getImage(), $excel->getActiveSheet()->getCell('B2')->getValue());
    }

    /**
     * @expectedException \LogicException
     */
    public function testSaveWithoutPrepare()
    {
        $exporter = new ExcelExporter([]);
        $exporter->save(sys_get_temp_dir().'image_thread_export.xlsx');
    }

    public function testSaveCreatesFile()
    {
        $exporter = new ExcelExporter([]);
        $exporter->prepare();
        $path = sys_get_temp_dir().'image_thread_export.xlsx';

        $exporter->save($path);

        $this->assertFileExists($path);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSaveInvalidPath()
    {
        $exporter = new ExcelExporter([]);
        $exporter->prepare();
        $path = 'aaa/bbb.xlsx';

        $exporter->save($path);

        $this->assertFileExists($path);
    }
}