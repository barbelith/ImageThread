<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const CACHE_KEY_VIEWS_COUNT = 'views_count';

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getRepository('AppBundle:Statistic')->addView();
        $this->get('imagethread.cache')->delete(self::CACHE_KEY_VIEWS_COUNT);

        return $this->render('@App/Default/index.html.twig');
    }

    /**
     * @param Request $request
     * @Route("/view/count", name="view_count")
     * @return JsonResponse
     */
    public function countAction(Request $request)
    {
        $cache = $this->container->get('imagethread.cache');

        if ($cache->contains(self::CACHE_KEY_VIEWS_COUNT)) {
            $count = $cache->fetch(self::CACHE_KEY_VIEWS_COUNT);
        } else {
            $count = $this->getDoctrine()->getRepository('AppBundle:Statistic')->getNumberViews();

            $cache->save(self::CACHE_KEY_VIEWS_COUNT, $count);
        }

        return new JsonResponse(
          array(
            'status' => 'ok',
            'content' => $this->get('translator')->trans('views_number', array('%views%' => $count))
          )
        );
    }
}
