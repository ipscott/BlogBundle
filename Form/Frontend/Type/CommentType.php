<?php

namespace Desarrolla2\Bundle\BlogBundle\Form\Frontend\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('content', 'textarea', array(
                    'required' => true,
                    'trim'     => true,
                ))
                ->add('userName', 'text', array(
                    'required' => true,
                    'trim'     => true,
                ))
                ->add('userEmail', 'text', array(
                    'required' => false,
                    'trim'     => true,
                ))
                ->add('userWeb', 'text', array(
                    'required' => false,
                    'trim'     => true,
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Desarrolla2\Bundle\BlogBundle\Form\Frontend\Model\CommentModel',
            'csrf_protection' => true,
        ));
    }

    public function getName()
    {
        return 'desarrolla2_bundle_blogbundle_commenttype';
    }

}