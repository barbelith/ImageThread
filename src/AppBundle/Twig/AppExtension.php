<?php


namespace AppBundle\Twig;


class AppExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('posts_count', array($this, 'getPostsCount')),
            new \Twig_SimpleFunction('views_count', array($this, 'getViewsCount')),
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