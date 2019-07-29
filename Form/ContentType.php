<?php
/**
 * ContentType
 */
namespace Wizin\Bundle\SimpleCmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class ContentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', Type\HiddenType::class);
        $builder->add('pathInfo', Type\TextType::class);
        $builder->add('title', Type\TextType::class);
        $builder->add('parameters', Type\CollectionType::class, ['entry_type' => Type\TextareaType::class, 'required' => false]);
        $builder->add('templateFile', Type\TextType::class, ['attr' => ['readonly' => true]]);
        $builder->add('active', Type\CheckboxType::class, ['required' => false]);
        $builder->add('save', Type\SubmitType::class, ['validation_groups' => false]);
        $builder->add('draft', Type\SubmitType::class, ['validation_groups' => false]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Content';
    }
}
