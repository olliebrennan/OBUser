<?php
namespace User\Form;
use ZfcBase\InputFilter\ProvidesEventsInputFilter;

class ResetPasswordFilter extends ProvidesEventsInputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'validators' => array(
                array('name' =>'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Password must be supplied'
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
            'name' => 'confirmPassword',
            'required' => true,
            'validators' => array(
                array('name' =>'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Password may not be empty'
                        ),
                    ),
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6,
                        'message' => 'Password must be at least 6 characters',
                    ),
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name' => 'identical',
                    'options' => array(
                        'token' => 'password',
                        'message' => 'Password and confirmation must match',
                    ),
                    'break_chain_on_failure' => true,
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
