<?php

namespace Hanzo\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CmsType extends AbstractType
{
    

    public function __construct()
    {
        // No Constructor
    }

    public function buildForm(FormBuilder $builder, array $options)
    {

      $builder->add('is_active', 'checkbox', array(
          'label'     => 'cms.edit.is_active',
          'translation_domain' => 'admin',
          'required'  => false
      ));

      $builder->add('title', null, array(
          'required' => TRUE,
          'translation_domain' => 'admin'
      ));

      $builder->add('path', null, array(
          'required' => TRUE,
          'translation_domain' => 'admin'
      ));

      $builder->add('content', 'textarea', array(
          'required' => FALSE,
          'translation_domain' => 'admin'
      ));

      $builder->add('settings', null, array(
          'required' => FALSE,
          'translation_domain' => 'admin'
      ));

    }

    public function getDefaultOptions(array $options)
    {
      return array(
        'data_class' => 'Hanzo\Model\Cms',
      );
    }

    public function getName()
    {
      return 'cms';
    }
}
