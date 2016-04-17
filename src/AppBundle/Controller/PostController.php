<?php


namespace AppBundle\Controller;


use Alchemy\Zippy\Zippy;
use AppBundle\Export\CsvExporter;
use AppBundle\Export\ExcelExporter;
use AppBundle\Form\Type\PostExportType;
use Doctrine\Common\Cache\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Post;
use AppBundle\Form\Type\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;


class PostController extends Controller
{
    const CACHE_KEY_POST_COUNT = 'post_count';

    /**
     * @param Request $request
     * @Route("/post/create", name="post_create")
     * @Template()
     * @return array
     */
    public function createAction(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('post_create'),
        ]);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $imageName = sha1(uniqid(mt_rand(), true)).'.'.$post->getImageUpload()->guessExtension();
                    $this->container->get('imagethread.image_manager')->saveImageOnDisk(
                      $post->getImageUpload(),
                      $imageName
                    );

                    $post->setImage($imageName);

                    $this->getDoctrine()->getManager()->persist($post);
                    $this->getDoctrine()->getManager()->flush();

                    $this->container->get('imagethread.cache')->delete(self::CACHE_KEY_POST_COUNT);

                    /** @var Cache $cacheDriver */
                    $cacheDriver = $this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl();
                    $cacheDriver->deleteAll();

                    $this->addFlash('success', $this->get('translator')->trans('post_created_successfully'));
                } catch (\Exception $e) {
                    $this->addFlash('danger', $this->get('translator')->trans('post_create_error'));
                }

                return $this->redirectToRoute('homepage');
            }
        }

        return [
          'form' => $form->createView(),
          'extends_base' => !$this->container->get('request_stack')->getParentRequest()
        ];
    }

    /**
     * @param Request $request
     * @Route("/post/list", name="post_list")
     * @Template()
     * @return array
     */
    public function listAction(Request $request)
    {
        $lastItem = $request->get('last_item', 0);
        $limit = $request->get('limit', 10);

        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getPostsWithOffset($lastItem, $limit);

        return [
          'posts' => $posts,
          'extends_base' => !$this->container->get('request_stack')->getParentRequest() && !$request->isXmlHttpRequest()
        ];
    }

    /**
     * @param Request $request
     * @Route("/post/export", name="post_export")
     * @Template()
     * @return array
     */
    public function exportAction(Request $request)
    {
        $form = $this->createForm(
          PostExportType::class,
          array(),
          [
            'action' => $this->generateUrl('post_export'),
          ]
        );

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $format = $form['export_type']->getData();
                $includeImages = $form['export_include_images']->getData();

                $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->createQueryBuilder('p')->getQuery()->iterate();

                if ('csv' === $format) {
                    $exporter = new CsvExporter($posts);
                    $filename = 'posts_export.csv';
                } else {
                    $exporter = new ExcelExporter($posts);
                    $filename = 'posts_export.xlsx';
                }

                $exporter->prepare();

                if ($includeImages) {
                    $filesystem = new Filesystem();
                    $folderName = 'posts_export_'.time().'_'.mt_rand();
                    $workDir = sys_get_temp_dir().$folderName;
                    $imageManager = $this->container->get('imagethread.image_manager');

                    $filesystem->mkdir($workDir);
                    $exporter->save($workDir.DIRECTORY_SEPARATOR.$filename);

                    $filesystem->mkdir($workDir.DIRECTORY_SEPARATOR.'images');

                    $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->createQueryBuilder('p')->getQuery()->iterate();

                    foreach ($posts as $row) {
                        /** @var Post $post */
                        $post = $row[0];

                        $originalImage = $imageManager->getImagePath($post->getImage());

                        if ($filesystem->exists($originalImage)) {
                            $destination = $workDir.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$post->getImage();
                            $filesystem->copy($originalImage, $destination);
                        }
                    }

                    $zippy = Zippy::load();
                    $zipPath = $workDir.'.zip';
                    $zippy->create($zipPath, $workDir);

                    return $this->createResponseFromFile($zipPath, 'posts_export.zip');
                } else {
                    $self = $this;

                    $response = new StreamedResponse(
                      function () use ($self, $exporter) {
                          $exporter->save('php://output');
                      }
                    );

                    $response->headers->set('Content-Type', 'application/force-download');
                    $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));

                    return $response;
                }
            }
        }

        return [
          'form' => $form->createView(),
          'extends_base' => !$this->container->get('request_stack')->getParentRequest() && !$request->isXmlHttpRequest()
        ];
    }

    /**
     * @param $localDownloadPath
     * @return Response
     */
    protected function createResponseFromFile($localDownloadPath, $name = null)
    {
        $response = new BinaryFileResponse($localDownloadPath);
        $response->trustXSendfileTypeHeader();
        $filename = $name ? $name : basename($localDownloadPath);
        $response->setContentDisposition(
          ResponseHeaderBag::DISPOSITION_INLINE,
          $filename,
          iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/post/count", name="post_count")
     * @return JsonResponse
     */
    public function countAction(Request $request)
    {
        $cache = $this->container->get('imagethread.cache');

        if ($cache->contains(self::CACHE_KEY_POST_COUNT)) {
            $count = $cache->fetch(self::CACHE_KEY_POST_COUNT);
        } else {
            $count = $this->getDoctrine()->getRepository('AppBundle:Post')->count();

            $cache->save(self::CACHE_KEY_POST_COUNT, $count);
        }

        return new JsonResponse(array(
            'status' => 'ok',
            'content' => $this->get('translator')->trans('posts_number', array('%posts%' => $count))
        ));
    }
}