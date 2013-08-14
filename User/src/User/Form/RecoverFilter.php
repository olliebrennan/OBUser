<?php
namespace User\Form;
use ZfcBase\InputFilter\ProvidesEventsInputFilter;

class RecoverFilter extends ProvidesEventsInputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array('name' =>'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Email address must be supplied'
                        ),
                    ),
                ),
            ),
            'filters' => array(
                array(
                    'name' => 'StringTrim'
                )
            )
        ));

        $this->getEventManager()->trigger('init', $this);
    }
}
