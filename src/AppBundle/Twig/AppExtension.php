<?php


namespace AppBundle\Twig;


use AppBundle\Manager\ImageManager;
use Doctrine\ORM\EntityManager;

class AppExtension extends \Twig_Extension
{
    /** @var  ImageManager */
    protected $imageManager;

    /** @var EntityManager  */
    protected $entityManager;
    
    /**
     * AppExtension constructor.
     * @param ImageManager $imageManager
     * @param EntityManager $entityManager
     */
    public function __construct(ImageManager $imageManager, EntityManager $entityManager)
    {
        $this->imageManager = $imageManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('posts_count', array($this, 'getPostsCount')),
            new \Twig_SimpleFunction('views_count', array($this, 'getViewsCount')),
        );
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('image_url', array($this, 'getImageUrl'))
        );
    }

    public function getPostsCount()
    {
        return $this->entityManager->getRepository('AppBundle:Post')->count();
    }

    public function getViewsCount()
    {
        return $this->entityManager->getRepository('AppBundle:Statistic')->getNumberViews();
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