<?php
namespace Role\Controller;

use Library\Mvc\Controller\RestfulController;
use Zend\View\Model\JsonModel;


/**
 * This controller handles all roles module requests.
 * 
 */
class RoleController extends RestfulController
{  
    protected $em;
    protected $_allowedMethod = 'GET';
    protected $request;
    protected $requestData;


    public function indexAction()
    {
            $this->em = $this->getEntityManager();

            $return = array();
        
            $roles = $this->em->getRepository('User\Entity\Role')->findAll();
            foreach ($roles as $role) {
                $return[] = $role->getExpectedArray(array('user' => $this->getLoggedUser()));
            }

            return new JsonModel(array("result"=> $return));
    }

}