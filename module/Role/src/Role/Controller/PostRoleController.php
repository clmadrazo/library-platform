<?php
namespace Role\Controller;

use Library\Mvc\Controller\RestfulController;
use Zend\View\Model\JsonModel;


/**
 * This controller handles all roles module requests.
 * 
 */
class PostRoleController extends RestfulController
{  
    protected $em;
    protected $customer;
    protected $_allowedMethod = 'POST';
    protected $request;
    protected $requestData;


    public function addAction()
    {
        $this->customer = $this->getLoggedUser()->getCustomer();
        $this->request = $this->getRequest();
        $this->requestData = $this->processBodyContent($this->request);


        $roleHelper = $this->getServiceLocator()->get('RoleHelper');
        $role = $roleHelper->saveRole($this->requestData, $this->customer,true);

        if (!is_null($role)) {
            $this->getResponse()->setStatusCode(200);
            $return = array($role->getExpectedArray());
        } else {
            $this->getResponse()->setStatusCode(404);
            $return = array("errors" => \User\Entity\Role::ERR_TRANSLATIONNAME_EXISTS);
        }

        return new JsonModel(array("result" => $return));
    }

    public function editAction()
    {
        $this->customer = $this->getLoggedUser()->getCustomer();
        $this->request = $this->getRequest();
        $this->requestData = $this->processBodyContent($this->request);
        $roleHelper = $this->getServiceLocator()->get('RoleHelper');
        $role = $roleHelper->saveRole($this->requestData, $this->customer,false);

        if (!is_null($role)) {
            $this->getResponse()->setStatusCode(200);
            $return = array($role->getExpectedArray());
        } else {
            $this->getResponse()->setStatusCode(404);
            $return = array("errors" => \User\Entity\Role::ERR_TRANSLATIONNAME_EXISTS);
        }

        return new JsonModel(array("result" => $return));
    }

    public function deleteAction()
    {
        $this->em = $this->getEntityManager();
        $this->request = $this->getRequest();
        $this->requestData = $this->processBodyContent($this->request);

        $id = $this->requestData['roleId'];

        if (!empty($id)){
            $role = $this->em->getRepository('User\Entity\Role')->find($id);
        }
        $role->setStatus(\User\Entity\Role::STATUS_INACTIVE);

        $result = $role->getExpectedArray();
        $this->em->persist($role);
        $this->em->flush();
        return new JsonModel(
            array("result" => $result)
        );
    }
}