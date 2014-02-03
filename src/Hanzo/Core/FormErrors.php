<?php
/**
 * Form error helper class
 *
 * @see http://shout.setfive.com/2012/02/09/symfony2-getting-all-errors-from-a-form-in-a-controller/
 * @author ulrik@bellcom.dk
 * @NICETO figure out how to get validation errors from "getters"
 */

namespace Hanzo\Core;

use Symfony\Component\Form\Form;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class FormErrors
{
    protected $allErrors = array();

    protected $translator;
    protected $form;
    protected $domain;

    /**
     * Get a Form error handler
     *
     * @param Form       $form       the form object
     * @param Translator $translator translation object
     * @param string     $domain     translation domain
     */
    public function __construct(Form $form, Translator $translator, $domain = 'messages')
    {
        $this->form = $form;
        $this->translator = $translator;
        $this->domain = $domain;

        $this->getAllErrors($form);
    }

    /**
     * Get form errors as string
     *
     * @return string
     */
    public function toString()
    {
        $out = '<ul class="form-errors">';
        foreach ($this->allErrors as $key => $errors) {
            $out .= '<li class="'.$key.'">';
            foreach ($errors as $error) {
                $out .= $this->translator->trans($error, array(), $this->domain) . '<br>';
            }
            $out = substr($out, 0, -4) . '</li>'."\n";
        }
        $out .= '</ul>'."\n";

        return $out;
    }

    /**
     * Get form errors as an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->allErrors;
    }

    protected function getAllErrors($children = null, $template = true)
    {
        //return [];
        $this->getAllFormErrors($children, $template = true);
        return $this->allErrors;
    }

    protected function getAllFormErrors($children, $template = true)
    {
        foreach ($children as $child) {
            if ($errors = $child->getErrors()) {
                foreach ($errors as $error) {
                    $this->allErrors[$child->getName()][] = $this->convertFormErrorObjToString($error);
                }
            }

//            if ($child->hasChildren()) {
//                $this->getAllErrors($child);
//            }
        }
    }

    protected function convertFormErrorObjToString($error)
    {
        $errorMessageTemplate = $error->getMessageTemplate();
        foreach ($error->getMessageParameters() as $key => $value) {
            $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
        }

        return $errorMessageTemplate;
    }
}
