<?php
/**
 * @category    PostRole
 * @package     Model
 * @subpackage  Helper
 */
namespace Role\Model\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;


/**
 * This helper takes care of Article's needed operations
 */
class RoleHelper implements ServiceLocatorAwareInterface
{
    protected $entityManager;
    
    /**
     * Helper function that persist a Role entity
     *
     * @return User\Entity\Role
     */
    public function saveRole($requestData, $customer, $isNew)
    {
        $em = $this->getEntityManager();
        $title = $requestData['title'];
        $status = $requestData['status'];
        $is_admin = $requestData['is_admin'];
        $roleId = (isset($requestData['roleId'])) ? $requestData['roleId'] : null;
        $postStatusId = (isset($requestData['statusId'])) ? $requestData['statusId'] : null;

        if ($roleId != null) {
            $role = $em->find('User\Entity\Role', $roleId);
        } else {
            $role = new \User\Entity\Role;
        }
        
        $role->setTitle($title);
        $role->setStatus(\User\Entity\Role::STATUS_ACTIVE);

        if ($role->isValid($isNew, $em)) {
            $em->persist($role);
            $em->flush();
            return $role;
        } else {
            return null;
        }
        return null;
    }
        
    public function getServiceLocator()
    {
        throw new \Exception('Not used');
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Returns the EntityManager
     *
     * @access protected
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        if (null === $this->entityManager) {
            $em = $this->serviceLocator->get('Doctrine\ORM\EntityManager');
            $this->setEntityManager($em);
        }
        return $this->entityManager;
    }
    
    /**
     * Sets the EntityManager
     *
     * @param EntityManager $em
     * @access protected
     * @return PostController
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->entityManager = $em;
        return $this;
    }

}
