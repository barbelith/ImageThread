<?php


namespace AppBundle\Command;


use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatePostsCommand
 * @package AppBundle\Command
 * @codeCoverageIgnore
 */
class CreatePostsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
          ->setName('imagethread:create-posts')
          ->setDescription('Create new posts in the database')
          ->addArgument('number', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = (int)$input->getArgument('number');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $imageManager = $this->getContainer()->get('imagethread.image_manager');
        $imageData = file_get_contents('http://dummyimage.com/600x400/000/ffffff&text=Cool+image');

        for ($i = 1; $i <= $number; $i++) {
            $post = new Post();
            $post->setTitle(sprintf('Post #%s in %s', $i, date('Y/m/d H:i:s')));
            $post->setImage(mt_rand().'-'.$i.'.png');
            $em->persist($post);

            file_put_contents($imageManager->getImagePath($post->getImage()), $imageData);
        }

        $em->flush();

        $output->write(sprintf('<info>Created %s posts</info>', $number));
    }
}