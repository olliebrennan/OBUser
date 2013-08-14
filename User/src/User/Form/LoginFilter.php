<?php
namespace User\Form;
use ZfcBase\InputFilter\ProvidesEventsInputFilter;

class LoginFilter extends ProvidesEventsInputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'identity',
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

        $this->add(array(
            'name' => 'credential',
            'required' => false,
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->getEventManager()->trigger('init', $this);
    }
}
