<?php
namespace Authentication\Model;

use Library\Mvc\Entity\BaseEntity;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

class UserCredentials extends BaseEntity
{
    private $_fields = array();

    public function exchangeArray($data)
    {
        $this->_fields['email'] = (!empty($data[0]['user'])) ? $data[0]['user'] : null;
        if (is_null($this->_fields['email'])) {
            $this->_fields['email'] = (!empty($data[0]['email'])) ? $data[0]['email'] : null;
        }
        $this->_fields['password'] = (!empty($data[0]['password'])) ? $data[0]['password'] : null;
    }

    public function isValid()
    {
        if (!$this->_inputFilter) {
            $this->_inputFilter = new InputFilter();
            $email = new Input('email');
            $email->getValidatorChain()
                ->addValidator(new Validator\EmailAddress());
    
            $password = new Input('password');
            $password->getValidatorChain()
                ->addValidator(new Validator\NotEmpty())
                ->addValidator(new Validator\StringLength(6));

            $this->_inputFilter->add($email)
                ->add($password);
        }

        $dirtyData = array(
            'email' => $this->_fields['email'],
            'password' => $this->_fields['password']
        );
        $this->_inputFilter->setData($dirtyData);

        return $this->_inputFilter->isValid();
    }

    public function getEmail()
    {
        return $this->_fields['email'];
    }

    public function getPassword()
    {
        return $this->_fields['password'];
    }
}