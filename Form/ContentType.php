<?php
/**
 * ContentType
 */
namespace Wizin\Bundle\SimpleCmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pathInfo');
    }

    public function getName()
    {
        return 'Content';
    }
}
