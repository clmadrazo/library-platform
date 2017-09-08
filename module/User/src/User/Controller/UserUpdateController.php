<?php

namespace User\Controller;

use Library\Mvc\Controller\RestfulController;
use User\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use Zend\View\Model\JsonModel;

/**
 * 
 */
class UserUpdateController extends RestfulController {

    public function activeUserAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $requestData = $this->processBodyContent($request);
        $userRepository = $em->getRepository('User\Entity\User');
        $user = $userRepository->find($requestData[0]['id']);

        if (!empty($user)) {
            $user->setStatus(\User\Entity\User::STATUS_ACTIVE);

            $em->persist($user);
            $em->flush();

            $this->getResponse()->setStatusCode(200);
            $return = array($user->getExpectedArray());

        }
        else {
            $this->getResponse()->setStatusCode(404);
            $return = array("errors" => \User\Entity\User::ERR_USER_NOT_FOUND);
        }

        return new JsonModel(array("result" => $return));
    }
}
