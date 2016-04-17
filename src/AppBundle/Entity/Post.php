<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\Table
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    protected $image;

    /** @var UploadedFile */
    protected $image_upload;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return UploadedFile
     */
    public function getImageUpload()
    {
        return $this->image_upload;
    }

    /**
     * @param UploadedFile $image_upload
     */
    public function setImageUpload($image_upload)
    {
        $this->image_upload = $image_upload;
    }
}