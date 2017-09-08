<?php

/**
 * @category    Profile
 * @package     Model
 * @subpackage  Helper
 */

namespace Profile\Model\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use User\Entity\User;

/**
 * This helper takes care of Article's needed operations
 */
class ProfileHelper implements ServiceLocatorAwareInterface {

    protected $entityManager;
    protected $_profileUrl = '';

    /**
     * Helper function that returns the user that matches the id provided.
     *
     * @return User\Entity\User
     */
    public function getUser($userId) {
        $em = $this->getEntityManager();

        $user = $em->getRepository('User\Entity\User')
                ->findOneBy(array('id' => $userId, 'status' => User::STATUS_ACTIVE));

        return $user;
    }

    /**
     * Helper function that returns the user that matches some identifier provided.
     * Valid identifiers could be: userId, username, email, profileUrl
     * @return User\Entity\User
     */
    public function getUserBy($requestData) {
        if (isset($requestData[0]['userId'])) {
            $user = $this->getUser($requestData[0]['userId']);
        } elseif (isset($requestData[0]['username'])) {
            $user = $this->getUserByUsername($requestData[0]['username']);
        } elseif (isset($requestData[0]['email'])) {
            $user = $this->getUserByEmail($requestData[0]['email']);
        } 

        return $user;
    }

    /**
     * Helper function that returns the user that matches the email provided.
     *
     * @return User\Entity\User
     */
    public function getUserByEmail($email) {
        $em = $this->getEntityManager();
        
        $filters = array('email' => $email, 'status' => User::STATUS_ACTIVE);
        $user = $em->getRepository('User\Entity\User')
                ->findOneBy($filters);

        return $user;
    }

    /**
     * Helper function that returns the user that matches the profileUrl provided.
     *
     * @return User\Entity\User
     */
    public function getUserByProfileUrl($profileUrl) {
        $em = $this->getEntityManager();

        $user = $em->getRepository('User\Entity\User')
                ->findOneBy(array('profile_url' => $profileUrl, 'status' => User::STATUS_ACTIVE));

        return $user;
    }

    /**
     * Helper function that returns the user that matches the username provided.
     *
     * @return User\Entity\User
     */
    public function getUserByUsername($username) {
        $em = $this->getEntityManager();

        $user = $em->getRepository('User\Entity\User')
                ->findOneBy(array('username' => $username, 'status' => User::STATUS_ACTIVE));

        return $user;
    }

    /**
     * Helper function that returns the user that matches the identity token provided.
     *
     * @return User\Entity\User
     */
    public function getUserByIdentityToken($identityToken) {
        $em = $this->getEntityManager();

        $user = $em->getRepository('User\Entity\User')
                ->findOneBy(array('social_token' => $identityToken, 'status' => User::STATUS_ACTIVE));

        return $user;
    }

    public function getServiceLocator() {
        throw new \Exception('Not used');
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Returns the EntityManager
     *
     * @access protected
     * @return EntityManager
     */
    protected function getEntityManager() {
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
    public function setEntityManager(EntityManager $em) {
        $this->entityManager = $em;
        return $this;
    }

    public function generateToken($user, $refresh = false) {
        $token = new \User\Entity\AccessToken;
        $val = bin2hex(openssl_random_pseudo_bytes(16));
        $refreshVal = bin2hex(openssl_random_pseudo_bytes(16));

        $token->setUser($user);
        $token->setValue($val);
        $token->setRefresh($refreshVal);
        $token->setCreated();
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        // Clean access_tokens table
        $this->_removeOldTokens($user);

        return ($refresh) ? $refreshVal : $val;
    }

    public function userCanExecute($user, $routeName) {
        foreach ($user->getRoles() as $role) {
            foreach ($role->getProcesses() as $process) {
                if ($process->getName() === $routeName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Helper function that returns the users that matches the ids provided.
     *
     * @return array
     */
    public function getUsers($usersIds) {
        $em = $this->getEntityManager();
        $ids = explode(',', $usersIds);

        $return = array();
        foreach ($ids as $id) {
            $user = $em->getRepository('User\Entity\User')
                    //->findOneBy(array('id' => $id, 'status' => User::STATUS_ACTIVE));
                    ->findOneBy(array('id' => $id));

            if (empty($user)) {
                return false;
            } else {
                $return[] = $user;
            }
        }

        return $return;
    }

    private function _removeOldTokens($user) {
        $em = $this->getEntityManager();

        $expiredTokens = $em->getRepository('User\Entity\AccessToken')
                ->findBy(array('user' => $user));

        foreach ($expiredTokens as $tok) {
            $now = new \DateTime();
            $diff = $tok->getCreated()->diff($now);
            $minutes = intval($diff->format('%y%m%d%h%i'));
            $res = ($minutes < 500) ? true : false;
            if (!$res) {
                // Old token
                $em->remove($tok);
                $em->flush();
            }
        }
    }

    public function createProfileUrl(array $requestData, $em = null, $mysqliconn = null) {
        if (null === $this->entityManager && !is_null($em)) {
            $this->setEntityManager($em);
        }

        $lastname = utf8_encode(str_replace(
                        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), substr(trim($requestData['lastname']), 0, 1)
        ));
        $existingUsers = $this->getEntityManager()->getRepository('User\Entity\User');

        if (count($existingUsers)) {
            $try = count($existingUsers) + 1;
        }

        return strtolower($this->_profileUrl);
    }

    /**
     * Reemplaza todos los acentos por sus equivalentes sin ellos
     *
     * @param $string
     *  string la cadena a sanear
     *
     * @return $string
     *  string saneada
     */
    private function sanitize_string($string) {

        $string = trim($string);

        $string = str_replace(
                array('ã', 'á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
                array(
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "<code>", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            ".", " "), '', $string
        );


        return $string;
    }

}
