<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Post;
use AppBundle\Form\Type\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class PostController extends Controller
{
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
}