<?php


namespace AppBundle\Export;


use AppBundle\Entity\Post;
use Doctrine\ORM\Internal\Hydration\IterableResult;

class CsvExporter implements ExporterInterface
{
    /** @var  Post[] */
    protected $posts;

    protected $data = [];

    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    public function prepare()
    {
        $this->data[] = ['Title', 'Filename'];

        foreach ($this->posts as $post) {
            if ($this->posts instanceof IterableResult) {
                $post = $post[0];
            }

            $this->data[] = [
                $post->getTitle(),
                $post->getImage()
            ];
        }

        return $this;
    }

    public function save($path)
    {
        if (0 === count($this->data)) {
            throw new \LogicException('The data is not prepared');
        }

        try {
            $handle = fopen($path, 'w+');

            foreach ($this->data as $row) {
                fputcsv($handle, $row, ';', '"');
            }

            fclose($handle);
        } catch (\Exception $e) {
            throw new \LogicException('Could not create the csv file');
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}