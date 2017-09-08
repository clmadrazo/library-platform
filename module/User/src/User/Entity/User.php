<?php

namespace User\Entity;

use Library\Mvc\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation\Object;
use Doctrine\ORM\EntityManager;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity Class representing a User of our Application.
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseEntity {

    protected $_validationErrors = array();

    /**
     * All available User statuses.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    //Error messages
    const ERR_EMAIL_INVALID = "Invalid email";
    const ERR_USERNAME_EXISTS = "Username already exists";
    const ERR_EMAIL_EXISTS = "Email address already exists";
    const ERR_LANGUAGE_INVALID = "Invalid language id";
    const ERR_USER_NOT_FOUND = "User doesn't exists";
    const DEFAULT_BIRTHDATE = null;
    const DEFAULT_COUNTRY_ID = 'ar';
    const DEFAULT_LANGUAGE_REGION_ID = 1;
    const DEFAULT_LANGUAGE_ID = 1;

    /**
     * Primary Identifier
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Username
     *
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * Password
     *
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * Name
     *
     * @ORM\Column(type="string")
     */
    protected $name = "";

    /**
     * LastName
     *
     * @ORM\Column(type="string")
     */
    protected $lastname = "";

    /**
     * Date of Birth
     *
     * @ORM\Column(name="date_of_birth", type="date")
     */
    protected $dateOfBirth;

    /**
     * Email
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * Timezone
     *
     * @ORM\Column(type="decimal", precision=10, scale=1)
     */
    protected $timezone = "";

    /**
     * Status
     *
     * @ORM\Column(type="smallint")
     */
    protected $status = "";

    /**
     * User Status table reference
     * @ORM\ManyToOne(targetEntity="User\Entity\UserStatus", fetch="EXTRA_LAZY")
     */
    protected $user_status;

    /**
     * User status id
     * @ORM\Column(type="integer")
     */
    protected $user_status_id;

    /**
     * Modified date
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * Created date
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $reset_password_code;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    protected $reset_password_code_expiration;

    /**
     * User Roles array
     * @ORM\OneToMany(targetEntity="RoleUser", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $user_roles;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager = null) {
        parent::__construct($entityManager);

        $this->dateOfBirth = new DateTime(self::DEFAULT_BIRTHDATE);
        $this->updated = new DateTime();
        $this->created = new DateTime();

        $this->user_roles = new ArrayCollection();
        $this->user_status = $this->_entityManager->find('User\Entity\UserStatus', 6);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * @return String
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param String $name
     * @return User
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return String
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param String $lastname
     * @return User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return String
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param String $dateOfBirth
     * @example 2001-12-23
     * @return User
     */
    public function setDateOfBirth($dateOfBirth) {
        $this->dateOfBirth = new DateTime($dateOfBirth);
        return $this;
    }

    /**
     * @return Object date
     */
    public function getDateOfBirth() {
        return $this->dateOfBirth;
    }

    /**
     * @param String $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return String
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param int $timezone
     * @return User
     */
    public function setTimezone($timezone) {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimezone() {
        return $this->timezone;
    }

    /**
     * @param int $status
     * @return User
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param integer $userStatusId
     * @return User
     */
    public function setUserStatusId($userStatusId) {
        $this->user_status_id = $userStatusId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserStatusId() {
        return $this->user_status_id;
    }

    /**
     * @param UserStatus $userStatus
     * @return User
     */
    public function setUserStatus(UserStatus $userStatus) {
        $this->user_status = $userStatus;
        return $this;
    }

    /**
     * @return UserStatusy
     */
    public function getUserStatus() {
        return $this->user_status;
    }

    public function getDefaultStatus() {
        return self::STATUS_PENDING;
    }

    public function getCreated() {
        return $this->created->format('Y-m-d H:i:s');
    }

    /**
     * @param string $resetPasswordCode
     * @return User
     */
    public function setResetPasswordCode($resetPasswordCode) {
        $this->reset_password_code = $resetPasswordCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getResetPasswordCode() {
        return $this->reset_password_code;
    }

    /**
     * @param datetime $resetPasswordCodeExpiration
     * @return User
     */
    public function setResetPasswordCodeExpiration($resetPasswordCodeExpiration) {
        $this->reset_password_code_expiration = $resetPasswordCodeExpiration;
        return $this;
    }

    /**
     * @return datetime
     */
    public function getResetPasswordCodeExpiration() {
        return $this->reset_password_code_expiration;
    }

    /**
     * @see Library\Mvc\Entity\BaseEntity
     */
    public function exchangeArray($data) {
        $this->id = (!empty($data['userId'])) ? $data['userId'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->created = (!empty($data['created'])) ? $data['created'] : null;
        $this->updated = (!empty($data['modified'])) ? $data['modified'] : null;
        $this->reset_password_code = (!empty($data['resetPasswordCode'])) ? $data['resetPasswordCode'] : null;
    }

    /**
     * @see Library\Mvc\Entity\BaseEntity
     */
    public function getCleanArrayCopy() {
        $arrayCopy = $this->getArrayCopy();
        unset($arrayCopy['password']);
        unset($arrayCopy['resetPasswordCode']);
        return $arrayCopy;
    }

    /**
     * Return a complex entity 
     */
    public function getExpectedSearchArray() {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'firstName' => $this->getName(),
            'lastName' => $this->getLastname(),
            'roles' => $this->getRoleIdsArray(),
            'roles_names' => $this->getRoleNamesArray(),
            'user_status' => $this->getUserStatus()->getId(),
            'created' => $this->getCreated(),
            'modified' => $this->updated,
        );
    }

    /**
     * @see Library\Mvc\Entity\BaseEntity
     */
    public function getExpectedArray($params = array()) {

        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'firstName' => $this->getName(),
            'lastName' => $this->getLastname(),
            'created' => $this->getCreated(),
            'modified' => $this->updated,
            'role' => $this->getRoleIdsArray(),
        );
    }

    /**
     * Return a complex entity 
     */
    public function getExpectedFullArray() {

        $return = array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'firstName' => $this->getName(),
            'lastName' => $this->getLastname(),
            'created' => $this->created,
            'modified' => $this->updated,
        );

        return $return;
    }

    public function getValidationErrors() {
        return $this->_validationErrors;
    }

    protected function addValidationError($errorMessage) {
        $this->_validationErrors[] = $errorMessage;
    }

    public function getUserRoles() {
        return $this->user_roles;
    }

    public function getRoles() {
        $roles = array();

        foreach ($this->user_roles as $userRole) {
            $roles[] = $userRole->getRole();
        }

        return $roles;
    }

    public function getRoleIdsArray() {
        $roles = array();

        foreach ($this->user_roles as $userRole) {
            $roles[] = $userRole->getRole()->getId();
        }

        return $roles;
    }

    public function getRoleNamesArray() {
        $roles = array();

        foreach ($this->user_roles as $userRole) {
            $roles[] = $userRole->getRole()->getTitle();
        }

        return $roles;
    }

    /**
     * Check if the user is valid
     * It checks: email address correct - email address and username
     * doesn't exists
     * @params boolean $isNew Indicate if this is a create action
     * @return boolean
     */
    public function isValid($isNew = false) {
        $return = true;
        $validatorEmailValid = new \Zend\Validator\EmailAddress();
        if (!$validatorEmailValid->isValid($this->email)) {
            $this->addValidationError(self::ERR_EMAIL_INVALID);
            $return = false;
        }

        //If it's an insert...
        if ($isNew) {
            //Check if the user exists
            if (!$this->_checkUserExists()) {
                $this->addValidationError(self::ERR_EMAIL_EXISTS);
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Check if the user exists in the database
     * It checks: email address and username doesn't exists
     * @return boolean
     */
    protected function _checkUserExists() {
        $user = $this->_entityManager->getRepository('User\Entity\User')->
                findBy(array(
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'status' => self::STATUS_ACTIVE,
        ));

        return (!empty($user)) ? false : true;
    }

    /*
     * This method populates a User entity with the request data
     * @params array $resquestData The request data
     *         boolean $isNew Indicate if this is a create action
     * @return User The user entity populated
     */

    public function getLoginData() {
        $data = array();
        $data['user'] = $this->getEmail();
        $data['userId'] = $this->getId();
        $data['password'] = $this->getPassword();

        return array($data);
    }

    public function getErrorMessages() {
        return !is_null($this->_inputFilter) ?
                $this->_inputFilter->getMessages() :
                array('errors' => $this->getValidationErrors());
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
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
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
