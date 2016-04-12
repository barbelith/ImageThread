<?php


namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, array('label' => 'image_title', 'attr' => array('class' => 'u-full-width')))
          ->add('image_upload', FileType::class, array('label' => 'select_image'))
          ->add('save', SubmitType::class, array('label' => 'create_post', 'attr' => array('class' => 'button-primary')));
    }
}