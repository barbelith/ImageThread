<?php


namespace AppBundle\Twig;


use AppBundle\Manager\ImageManager;

class AppExtension extends \Twig_Extension
{
    /** @var  ImageManager */
    protected $imageManager;

    /**
     * AppExtension constructor.
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('posts_count', array($this, 'getPostsCount')),
            new \Twig_SimpleFunction('views_count', array($this, 'getViewsCount')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('image_url', array($this, 'getImageUrl'))
        );
    }

    public function getPostsCount()
    {
        return 5;
    }

    public function getViewsCount()
    {
        return 2;
    }
    
    public function getImageUrl($imageName)
    {
        return $this->imageManager->getImageUrl($imageName);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_extension';
    }
}