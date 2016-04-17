<?php


namespace AppBundle\Export;


use AppBundle\Entity\Post;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;

class ExcelExporter implements ExporterInterface
{
    /** @var  PHPExcel */
    protected $excel;

    /** @var  Post[] */
    protected $posts;

    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    public function prepare()
    {
        $this->excel = new PHPExcel();

        $this->excel->getProperties()->setCreator('Image Thread')
          ->setLastModifiedBy('Image Thread')
          ->setTitle('Posts export');

        $sheet = $this->excel->getActiveSheet();

        $this->addHeaderCell($sheet, 'Title', 0, 1);
        $this->addHeaderCell($sheet, 'Filename', 1, 1);

        $sheet->getColumnDimension('B')->setWidth(100);

        $data = [];

        foreach ($this->posts as $post) {
            if ($this->posts instanceof IterableResult) {
                $post = $post[0];
            }

            $data[] = [
                $post->getTitle(),
                $post->getImage()
            ];
        }

        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');

        return $this;
    }

    public function save($path)
    {
        if (!$this->excel) {
            throw new \LogicException('The excel file is not prepared');
        }

        try {
            /** @var \PHPExcel_Writer_Excel2007 $objWriter */
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            $objWriter->save($path);
        } catch (\Exception $e) {
            throw new \LogicException('Could not create the excel file');
        }
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     */
    protected function addHeaderCell($sheet, $text, $x, $y)
    {
        $sheet->setCellValueByColumnAndRow($x, $y, $text);
        $style = $sheet->getStyleByColumnAndRow($x, $y);
        $style->getFont()->setBold(true)->setSize(10);
        $style->applyFromArray(
          array(
            'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb' => '9DC3E5')
            )
          )
        );
    }

    /**
     * @return PHPExcel
     */
    public function getExcel()
    {
        return $this->excel;
    }
}