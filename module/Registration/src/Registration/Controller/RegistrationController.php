<?php

namespace Registration\Controller;

use Authentication\Model\Helper\AuthenticationHelper;
use Library\Mvc\Controller\RestfulController;
use User\Entity;
use Zend\View\Model\JsonModel;

/**
 * 
 */
class RegistrationController extends RestfulController {

    protected $_allowedMethod = "post";
    private $em;
    private $alert;

    /**
     * This function will manage the request to create a new User
     * @example
     *  [Request]
     *      POST /registration
     * @return an array model that contains the user created if it was
     * succesful, or containing an 'errors' list
     */
    public function indexAction() {
        $requestData = $this->processBodyContent($this->getRequest());
        $return = $this->doRegistration($requestData);

        return new JsonModel($return);
    }

    public function userRegistrationAction() {
        $requestData = $this->processBodyContent($this->getRequest());

        $return = $this->doUserRegistration($requestData);

        return new JsonModel($return);
    }

    /*
     * This method populates a User entity with the request data
     * @params array $resquestData The request data
     * @return User The user entity populated
     */

    private function _fillUser(array $requestData) {
        $em = $this->getEntityManager();
        $authHelper = new AuthenticationHelper();
        $id = (isset($requestData[0]['userId'])) ? $requestData[0]['userId'] : null;
        if (!isset($requestData[0]['noActivation']) || (isset($requestData[0]['noActivation']) && ($requestData[0]['noActivation'] != 1))) {
            $this->alert = 1;
        }
        if (!is_null($id)) {
            $user = $this->em->getRepository('User\Entity\User')->find($id);
            $user->setStatus(\User\Entity\User::STATUS_ACTIVE);
        } else {
            $user = new \User\Entity\User($this->em);
            $user->setStatus(\User\Entity\User::STATUS_INACTIVE);
        }
        $user->setName($requestData[0]['name']);
        if (isset($requestData[0]['lastName'])) {
            $user->setLastname($requestData[0]['lastName']);
            $lastname = $requestData[0]['lastName'];
        } else {
            $user->setLastname($requestData[0]['lastname']);
            $lastname = $requestData[0]['lastname'];
        }
        if (isset($requestData[0]['email']))
            $user->setEmail($requestData[0]['email']);
        if (isset($requestData[0]['dateOfBirth']))
            $user->setDateOfBirth($requestData[0]['dateOfBirth']);
        
        if (isset($requestData[0]['password']) && $requestData[0]['password'] != '') {
            $user->setPassword($authHelper->hash($requestData[0]['password'], null, true));
        }
        if (!isset($requestData[0]['password']) || $requestData[0]['password'] === '') {
            $user->setPassword($authHelper->hash('123456', null, true));
        }

        return $user;
    }

    public function doRegistration($data, $em = null, $noActivation = false) {
        if (is_null($em)) {
            $em = $this->getEntityManager();
        } else {
            $this->setEntityManager($em);
        }
        $this->em = $em;
        if (isset($data[0]['email'])) {
            $aux = $this->em->getRepository('User\Entity\User')->findOneBy(array('email' => $data[0]['email']));
            if ($aux != null) {
                $this->getResponse()->setStatusCode(404);
                return array("error" => "Email já existe");
            }
        }

        if ((isset($data[0]['registrationCode'])) && ($data[0]['registrationCode'] != "undefined")) {
            $userInvitation = $em->getRepository('User\Entity\UserInvitation')->findOneBy(array('registration_code' => $data[0]['registrationCode']));
            if (empty($userInvitation)) {
                $this->getResponse()->setStatusCode(404);
                return array("error" => "Email já existe");
            } else {
                $aux = $this->em->getRepository('User\Entity\User')->findOneBy(array('email' => $userInvitation->getEmail()));
                $data[0]['userId'] = $aux->getId();
            }
        }
        $user = $this->_fillUser($data);
        $new = false;
        if (!empty($userInvitation)) {
            //$new = true;
            $user->setUsername($userInvitation->getEmail());
            $user->setEmail($userInvitation->getEmail());
            $user->setCustomer($userInvitation->getCustomer());
        }
        $user->setProfileUrl('');
        $user->setTimezone(null);

        if ($user->isValid($new)) {
            $aux = $em->getRepository('User\Entity\RoleUser')->findBy(array('user' => $user->getId()));
            if (!$aux) {
                $roleUser = new \User\Entity\RoleUser();
                $roleRepository = $em->getRepository('User\Entity\Role');
                if (!empty($userInvitation)) {
                    $em->persist($user);
                    $em->getConnection()->exec("update user_invitations set status='1' where email = '" . $userInvitation->getEmail() . "'");
                    $em->flush();
                    $role = $roleRepository->find($userInvitation->getRole()->getId());
                } else {
                    if (isset($data[0]['role_id'])) {
                        $role = $roleRepository->find($data[0]['role_id']);
                    } else {
                        $role = $roleRepository->find(1);
                    }
                }
                $roleUser->setRole($role);
                $roleUser->setUser($user);
                $em->persist($roleUser);
            }
            $user->setRelativePath('img/user-shape.png');
            if (isset($data[0]['email'])) {
                $user->setUsername($data[0]['email']);
            }
            $em->persist($user);
            $em->flush();
    
            $return = $user->getExpectedArray();
        } else {
            $this->getResponse()->setStatusCode(404);
            $return = $user->getErrorMessages();
        }

        return $return;
    }

}
