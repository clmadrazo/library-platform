<?php

namespace User\Controller;

use Library\Mvc\Controller\RestfulController;
use User\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use Zend\View\Model\JsonModel;

/**
 * 
 */
class UserRoleUpdateController extends RestfulController {

    protected $_allowedMethod = 'post';

    public function indexAction(){
        $requestData = $this->processBodyContent($this->getRequest());
        $userId = $this->getEvent()->getRouteMatch()->getParam('userId');
        $em = $this->getEntityManager();

        $user = $em->getRepository('User\Entity\User')->find($userId);
        $role = $requestData[0]['role'];
        $em->beginTransaction();
        $em->getConnection()->exec("DELETE FROM roles_users where user_id =$userId");
        foreach($role as $roleId){
            $roleEntity = $em->getRepository('User\Entity\Role')->find($roleId['roleId']);
            $roleUser = new \User\Entity\RoleUser($em); //$em->getRepository('User\Entity\RoleUser');
            $roleUser->setRole($roleEntity);
            $roleUser->setUser($user);
            $return[] =$roleUser;
            $em->persist($roleUser);
        }
        $em->flush();
        $em->commit();
        return new JsonModel(array("result" => $return));
    }


}
