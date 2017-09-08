<?php
/**
 * @category    Authentication
 * @package     Mvc
 * @subpackage  Entity
 */
namespace Library\Mvc\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * This base class provides a number of methods that are usually
 * very necessary when working with entities in order to avoid
 * code repetition.
 */
class BaseEntity implements \Doctrine\Common\Persistence\ObjectManagerAware
{
    /**
     * @var EntityManager
     */
    protected $_entityManager;
    protected $_inputFilter;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager = null) {
        if ($entityManager instanceof \Doctrine\ORM\EntityManager && !is_null($entityManager)) {
            $this->setEntityManager($entityManager);
        }
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
     */
    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata)
    {
        $this->setEntityManager($objectManager);
    }

    /**
     * Sets the EntityManager
     *
     * @param Doctrine\ORM\EntityManager $em
     * @access protected
     * @return BaseEntity
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * Returns the EntityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * It will return an array generated from entity
     * only with the expected attributes
     */
    public function getExpectedArray($params = array())
    {
        throw new \RuntimeException('You are required to implement this method.');
    }

    /**
     * Expects to receive an array from which it will extract
     * the values it recognizes to stored them in this class.
     */
    public function exchangeArray($data)
    {
        throw new \RuntimeException('You are required to implement this method.');
    }

    /**
     * Returns an array representation of this object.
     * 
     * @return Array
     */
    public function getArrayCopy()
    { 
        return get_object_vars($this);
    }

    /**
     * Returns a filtered or clean array representation. You could
     * use this to unset unwanted elements or fields that contain
     * sensible data that should not be exposed.
     * 
     * @return Array
     */
    public function getCleanArrayCopy()
    {
        return $this->getArrayCopy();
    }

    /**
     * A shortened way to get the value of an array if the given
     * key exists, otherwise returning a fallback value.
     */
    protected function _getValue($data, $key, $default = null)
    {
        $value = $default;
        if (!empty($data[$key])) {
            $value = $data[$key];
        }
        return $value;
    }

    public function getErrorMessages()
    {
        return !is_null($this->_inputFilter) ?
            $this->_inputFilter->getMessages() : '';
    }
}
