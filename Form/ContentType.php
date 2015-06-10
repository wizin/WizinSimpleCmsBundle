<?php
/**
 * ContentType
 */
namespace Wizin\Bundle\SimpleCmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('pathInfo');
        $builder->add('title');
        $builder->add('parameters', 'collection', ['type' => 'textarea', 'required' => false]);
        $builder->add('templateFile', 'text', ['read_only' => true]);
        $builder->add('active', 'checkbox', ['required' => false]);
        $builder->add('save', 'submit', ['validation_groups' => false]);
        $builder->add('draft', 'submit', ['validation_groups' => false]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Content';
    }
}
