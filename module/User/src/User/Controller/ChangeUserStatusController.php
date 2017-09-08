<?php

namespace User\Controller;

use Library\Mvc\Controller\RestfulController;
use Zend\View\Model\JsonModel;

/**
 * This controller handles
 *
 */
class ChangeUserStatusController extends RestfulController {

    protected $_allowedMethod = "post";

    public function indexAction() {

        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $requestData = $this->processBodyContent($request);
        $user = $em->getRepository('User\Entity\User')->find($requestData[0]['userId']);
        $userStatus = $em->getRepository('User\Entity\UserStatus')->find($requestData[0]['statusId']);

        if (!empty($user)) {
            $user->setUserStatus($userStatus);
            $em->persist($userStatus);
            $em->flush();

            $this->getResponse()->setStatusCode(200);
            $return = array($user->getExpectedArray());
        } else {
            $this->getResponse()->setStatusCode(404);
            $return = array("errors" => \User\Entity\User::ERR_USER_NOT_FOUND);
        }

        return new JsonModel(array("result" => $return));
    }

}


