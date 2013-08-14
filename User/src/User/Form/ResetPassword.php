<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;

class ResetPassword extends ProvidesEventsForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->add(array(
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'type' => 'password',
                'class' => 'input-medium',
                'placeholder' => 'Password',
            ),
        ));

        $this->add(array(
            'name' => 'confirmPassword',
            'options' => array(
                'label' => 'Confirm Password',
            ),
            'attributes' => array(
                'type' => 'password',
                'class' => 'input-medium',
                'placeholder' => 'Confirm Password',
            ),
        ));


        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Change Password')
            ->setAttributes(array(
                'type'  => 'submit',
            ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));

        $this->getEventManager()->trigger('init', $this);
    }
}
