<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Validator\ObjectExists;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class Role extends \Library\Mvc\Entity\BaseEntity {

    const ERR_ROLE_NOT_FOUND = "Role doesn't exists";
    const STATUS_ACTIVE = 1;

    protected $_validationErrors = array();

    //Error messages
    const ERR_TRANSLATIONNAME_EXISTS = "Role name already exists";
    const ERR_TRANSLATION_NOT_FOUND = "Role doesn't exists";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $title;

    /**
     * Status
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @param bool $status
     * @return User
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager = null) {
        $this->role_processes = new ArrayCollection();
        parent::__construct($entityManager);
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Check if the role is valid
     * doesn't exists
     * @params boolean $isNew Indicate if this is a create action
     * @return boolean
     */
    public function isValid($isNew = false, $entityManager) {
        $return = true;
        //If it's an insert...
        if ($isNew) {
            //Check if the role exists
            if ($this->_checkRoleExists($entityManager)) {
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Check if the role exists in the database
     * @return boolean
     */
    protected function _checkRoleExists($entityManager) {
        $return = false;

        $existingRole = $entityManager->getRepository('User\Entity\Role')
                ->findOneBy(array('title' => $this->getTitle()));

        if (!empty($existingRole)) {
            $return = true;
        }

        return $return;
    }

    protected function addValidationError($errorMessage) {
        $this->_validationErrors[] = $errorMessage;
    }

    public function getValidationErrors() {
        return $this->_validationErrors;
    }

    /**
     * @see Library\Mvc\Entity\BaseEntity
     */
    public function getExpectedArray($params = array()) {
        $title = $this->getTitle();
        return array(
            'id' => $this->getId(),
            'title' => $title,
        );
    }

}
