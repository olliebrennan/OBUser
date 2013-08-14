<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;

class Recover extends ProvidesEventsForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => '',
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Email',
            ),
        ));

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Recover!')
            ->setAttributes(array(
                'type'  => 'submit',
            ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));

        $this->getEventManager()->trigger('init', $this);
    }
}
