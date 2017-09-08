<?php
namespace User\Model\InputFilter;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

/**
 * Filter that encapsulates all filtering and validation that
 * applies to User entities data.
 */
class UserFilter implements InputFilterAwareInterface
{
    /**
     * @see Zend\InputFilter\InputFilterAwareInterface
     */
    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory     = new InputFactory();
        $inputFilter->add($factory->createInput(
            array(
                'name' => 'email',
                'required'=> true,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    )
                ),
            )
        ));
        $inputFilter->add($factory->createInput(
            array(
                'name' => 'password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                ),
            )
        ));
        return $inputFilter;
    }

    /**
     * @see Zend\InputFilter\InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
}
