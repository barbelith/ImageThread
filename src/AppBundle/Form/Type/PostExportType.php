<?php


namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PostExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('export_type', ChoiceType::class, array('label' => 'export_type', 'choices' => array('format_excel' => 'excel', 'format_csv' => 'csv')))
          ->add('export_include_images', CheckboxType::class, array('label' => 'export_include_images'))
          ->add('export', SubmitType::class, array('label' => 'export_posts', 'attr' => array('class' => 'button-primary')));
    }
}