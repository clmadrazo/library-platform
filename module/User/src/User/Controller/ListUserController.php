<?php

namespace User\Controller;

use Library\Mvc\Controller\RestfulController;
use Zend\View\Model\JsonModel;

/**
 * This controller handles all user module requests.
 *
 */
class ListUserController extends RestfulController {

    protected $_allowedMethod = "get";
    protected $em;
    protected $invitations;

    /**
     * @example
     *  [Request]
     *      GET /user/list
     *      Content-Type: application/json
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function indexAction() {
        $this->em = $this->getEntityManager();
        return $this->getListUser();
    }

    private function getListUser() {
        $query = "SELECT u.id FROM users ";
        $res = $this->getEntityManager()->getConnection()->query($query);
        $result = $res->fetchAll();

        foreach ($result as $userId) {
            $user = $this->getEntityManager()->getRepository('User\Entity\User')->find($userId['id']);
            $resultArray[] = $user->getExpectedArray();
        }

        return new JsonModel(array("result" => $resultArray));
    }

    private function getListUserPageManage() {
        $roles = $this->em->createQuery("SELECT r.id , r.title from \User\Entity\Role r
                                  WHERE
                                  r.customer = ?1
                                  ")->setParameter(1,$this->customer)->getResult();
        foreach ($roles as $key => $role){
            $roleArray[$key] = $this->getUserRoles($role['id']);
            $this->populateInvitations($role['id'],$roleArray[$key]);
            $result[$key] = array( $role['title'],$role['id'], $roleArray[$key]);
        }

        return new JsonModel(array("result" => $result));
    }

    private function getUserRoles($roleId) {
        $query = $this->em->createQuery("SELECT DISTINCT ru FROM User\Entity\RoleUser ru
                                    JOIN ru.role r
                                    JOIN ru.user u
                                    WHERE
                                    u.status = 1
                                    and r.id=$roleId");
        $queryResult = $query->getResult();
        $resultArray = null;
        foreach ($queryResult as $rec) {
            $resultArray[] = $rec->getExpectedArray();
        }
        return $resultArray;
    }

    private function populateInvitations($role, &$arrayResult) {
        if ($this->invitations) {
            $query = $this->em->createQuery("SELECT  ue FROM User\Entity\UserInvitation ue
                                    JOIN ue.role r
                                    where
                                    r.id= $role
                                    and (ue.status is NULL or ue.status=0)");

            $arrayInvite = $query->getResult();
            $addedUsers = array();
            foreach ($arrayInvite as $rec) {
                if (!in_array($rec->getId(), $addedUsers)) {
                    $addedUsers[] = $rec->getId();
                    $arrayResult[] = array("user" => $rec->getExpectedArray(), "invitation" => 1);
                }
            }
        }
    }

}
