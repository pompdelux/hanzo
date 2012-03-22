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
          'label'     => 'cms.edit.label.is_active',
          'translation_domain' => 'admin',
          'required'  => false
      ));

      $builder->add('title', null, array(
          'label'     => 'cms.edit.label.title',
          'required' => TRUE,
          'translation_domain' => 'admin'
      ));

      $builder->add('path', null, array(
          'label'     => 'cms.edit.label.path',
          'required' => TRUE,
          'translation_domain' => 'admin'
      ));

      $builder->add('content', 'textarea', array(
          'label'     => 'cms.edit.label.content',
          'required' => FALSE,
          'translation_domain' => 'admin'
      ));

      $builder->add('settings', null, array(
          'label'     => 'cms.edit.label.settings',
          'required' => FALSE,
          'translation_domain' => 'admin'
      ));

    }

    public function getDefaultOptions(array $options)
    {
      return array(
        'data_class' => 'Hanzo\Model\CmsI18n',
      );
    }

    public function getName()
    {
      return 'cmsI18n';
    }

}
