<?php

/**
 * @category    Library
 * @package     Mvc
 * @subpackage  Controller
 */

namespace Library\Mvc\Controller;

use Doctrine\ORM\EntityManager;
use Zend\View\Model\JsonModel;
use Zend\Config\Reader\Ini,
    Zend\Http\Request,
    Zend\Json\Json,
    Zend\Mvc\Controller\AbstractRestfulController,
    Zend\Mvc\MvcEvent;

/**
 * Library simplified RESTful controller. What are the benefits
 * of having such a base class for our controllers?
 * 
 *    - We can write additional methods that would be used by all controllers
 *    - It adds a separation layer in case we need to move to a different framework
 *    - We can write initialization code that applies to all controllers
 *    - Or we could add a config-based set-up that every controller defines
 */
class RestfulController extends AbstractRestfulController {

    const PROCESS_REQUEST_ERROR = 'There was an error while processing the request';
    const PROCESS_REQUEST_UNAUTHORIZED = 'Not Authorized';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Logger
     */
    protected $logger;
    protected $_requiredFields = array();
    protected $_optionalFields = array();

    /**
     * The associated request object
     * @var RequestInterface 
     */
    private $_request;

    /**
     * Request ID
     * @var string
     */
    private $requestId;

    /**
     * Service Configuration data
     * @var array|null 
     */
    private static $_serviceConfig = null;

    /**
     * Role Authenticated User
     * @var User
     */
    private $_loggedUser = null;

    /**
     * Actual Bearer-Token
     * @var array
     */
    private $_response = null;
    private $_bearer = null;

    /**
     * This method makes sure this is a valid RESTful request, that is:
     *  - Content-Type is mandatory and it must be one of those defined
     *    in the parent class.
     *  - On POST, the request body is empty
     *  - @todo also when it's not a valid JSON
     * 
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e) {
        $this->_setServiceConfig();
        $response = $e->getResponse();
        $request = $e->getRequest();
        $this->_request = $request;

        $forceResponse = $request->getHeaders()->get('Force-Response');
        if ($forceResponse) {
            $code = str_replace(';', '', $forceResponse->getFieldValue('Force-Response'));
            $responseBody = $request->getHeaders()->get('Response-Body');
            if ($code === '404' && $responseBody) {
                $content = str_replace(';', '', $responseBody->getFieldValue('Force-Response'));
                if (!json_decode($content)) {
                    $response->setContent('Invalid Response-Body value. You must send a valid JSON.');
                } else {
                    $response->setContent($content);
                }
            }
            return $response->setStatusCode($code);
        }

        $headers = $response->getHeaders();
        $headers->addHeaderLine('X-Transaction', $this->getRequestId());
        $httpMethod = $this->getRequest()->getMethod();

        if ($httpMethod !== strtoupper($this->_allowedMethod)) {
            $response->setStatusCode(400);
            return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
        }

        $routeName = $e->getRouteMatch()->getMatchedRouteName();
        $token = $request->getHeaders()->get('Bearer-Token');
        $this->_bearer = $token;
        if ((!$this->requestHasContentType($request, self::CONTENT_TYPE_JSON) && (!$request->getHeaders()->get('Content-Type') === false)) && $routeName !== 'getCheckedFlickrImages' && $routeName !== 'exportPost' && $routeName !== 'exportUser' && $routeName !== 'exportProject') {
            $response->setStatusCode(400);
            return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
        } else if (!$token) {
            if ($routeName !== 'registration' && $routeName !== 'freelanceRegistration' && $routeName !== 'getUserPublishedArticle' && $routeName !== 'set-password' && $routeName !== 'getUserByProfileURL' && $routeName !== 'getUser' && $routeName !== 'updateProfile' && $routeName !== 'joinTeam' && $routeName !== 'addTestArticle' && $routeName !== 'forgot-password' && $routeName !== 'getQuestionsTest' && $routeName !== 'updatePostWorkflowStatusCron' && $routeName !== 'returnPostToBag' && $routeName !== 'assessmentQuestionsTest' && $routeName !== 'checkEmail' && $routeName !== 'getCheckedFlickrImages' && $routeName !== 'valid-reset-password' && $routeName !== 'login' && $routeName !== 'listSkills' && $routeName !== 'list-language-region' && $routeName !== 'list-topic' && $routeName !== 'activateUser' && $routeName !== 'listTranslation' && $routeName !== 'refreshToken' && $routeName !== 'exportPost' && $routeName !== 'exportUser' && $routeName !== 'exportProject' && $routeName !== 'search-user' && $routeName !== 'importViviliaUsers' && $routeName !== 'importDevUsers' && $routeName !== 'checkClientSocialTokens' && $routeName !== 'validateToken' && $routeName !== 'testing') {
                $response->setStatusCode(400);
                return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
            }
        } else if (in_array($httpMethod, [Request::METHOD_POST, Request::METHOD_PUT])) {
            $this->_setRequiredFields($routeName);
            $this->_setOptionalFields($routeName);
            if (!$this->_checkRequestContent($request) || !$this->_userIsAllowedToExecute($token, $routeName)) {
                $response->setStatusCode(400);
                return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
            } else {
                $authenticationHelper = $this->getServiceLocator()->get('AuthenticationHelper');
                switch ($authenticationHelper->getTokenStatus($token)) {
                    case 0:     // Invalid Token
                        $response->setStatusCode(400);
                        return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
                    case 2:     // Old Token
                        $response->setStatusCode(401);
                        return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_UNAUTHORIZED]));
                }
                $this->_setLoggedUser($token);
            }
        } else if (in_array($httpMethod, [Request::METHOD_GET])) {
            if (!$this->_userIsAllowedToExecute($token, $routeName)) {
                $response->setStatusCode(400);
                return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
            } else {
                $authenticationHelper = $this->getServiceLocator()->get('AuthenticationHelper');
                if ($routeName !== 'search-user' && $routeName !== 'importViviliaUsers') {
                    switch ($authenticationHelper->getTokenStatus($token)) {
                        case 0:     // Invalid Token
                            $response->setStatusCode(400);
                            return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_ERROR]));
                        case 2:     // Old Token
                            if ($routeName !== 'listTranslation') {
                                $response->setStatusCode(401);
                                return $response->setContent(Json::encode((object) ['error' => self::PROCESS_REQUEST_UNAUTHORIZED]));
                            }
                    }
                }
                $this->_setLoggedUser($token);
            }
        }

        return parent::onDispatch($e);
    }

    /**
     * Check if the request contains all the required fields
     * 
     * @param \Zend\Http\PhpEnvironment\Request $request
     * @return boolean
     */
    protected function _checkRequestContent(\Zend\Http\PhpEnvironment\Request $request) {
        $return = true;
        $content = $request->getContent();

        if (empty($content)) {
            $return = false;
        } else {

            $oParams = $this->_parseRequestBody();
            $oParams = is_array($oParams) ? $oParams : (is_object($oParams) ? [$oParams] : false);

            if (!is_array($oParams)) {
                $return = false;
            } else if (!empty($oParams)) {
                $requireds = $this->_checkRequiredFields($oParams);
                $remainings = $this->_checkRemainingFields($oParams);
                $return = ($requireds && $remainings);
            } else {
                $return = false;
            }
        }

        return $return;
    }

    public function create($data) {
        $this->notFoundAction();
    }

    public function get($id) {
        $this->notFoundAction();
    }

    public function getList() {
        $this->notFoundAction();
    }

    public function update($id, $data) {
        $this->notFoundAction();
    }

    public function delete($id) {
        $this->notFoundAction();
    }

    public function getRequestId() {
        if (!isset($this->requestId)) {
            $this->requestId = md5(\Library\Util\DateUtils::rightNow() . microtime());
        }
        return $this->requestId;
    }

    /**
     * Sets the EntityManager
     *
     * @param EntityManager $em
     * @access protected
     * @return PostController
     */
    protected function setEntityManager(EntityManager $em) {
        $this->entityManager = $em;
        return $this;
    }

    /**
     * Returns the EntityManager
     *
     * Fetches the EntityManager from ServiceLocator if it has not been initiated
     * and then returns it
     *
     * @access protected
     * @return EntityManager
     */
    protected function getEntityManager() {
        if (null === $this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }

    protected function sortResult(array $result, $fieldName) {
        $aux = array();

        foreach ($result as $key => $row) {
            $aux[$key] = $row[$fieldName];
        }
        array_multisort($aux, SORT_ASC, $result);

        return $result;
    }

    protected function getLogger() {
        if (null === $this->logger) {
            $this->setLogger($this->getServiceLocator()->get('Errors\Logger'));
        }
        return $this->logger;
    }

    protected function setLogger($logger) {
        $this->logger = $logger;
        return $this;
    }

    protected function log($message, $type = \Zend\Log\Logger::DEBUG) {
        $logger = $this->getLogger();
        $logger->log($type, $message);
    }

    /**
     * 
     * @param string $routeName
     * @throws \Exception
     */
    private function _setRequiredFields($routeName) {
        $result = array();

        if (!isset(self::$_serviceConfig['requireds'][$routeName])) {
            throw new \Exception("Missing required attrs $routeName", 400);
        }
        $requireds = explode(",", self::$_serviceConfig['requireds'][$routeName]);

        foreach ($requireds as $required) {
            $required = trim($required);
            $result[] = $required;
        }

        $this->_requiredFields = $result;
    }

    /**
     * 
     * @param string $routeName
     */
    private function _setOptionalFields($routeName) {
        $result = array();

        if (isset(self::$_serviceConfig['optionals'][$routeName])) {
            $optionals = explode(",", self::$_serviceConfig['optionals'][$routeName]);

            foreach ($optionals as $optional) {
                $optional = trim($optional);
                $result[] = $optional;
            }
        }

        $this->_optionalFields = $result;
    }

    /**
     * 
     * @param array $oParams
     * @return boolean
     */
    private function _checkRequiredFields(array $oParams) {
        $return = true;
        foreach ($this->_requiredFields as $requiredField) {
            $strings = explode('|', $requiredField);
            $return = false;

            foreach ($strings as $string) {
                if (isset($oParams[0]->$string)) {
                    $return = true;
                }
            }

            if (!$return) {
                break;
            }
        }

        return $return;
    }

    /**
     * 
     * @param array $oParams
     * @return boolean
     */
    private function _checkRemainingFields(array $oParams) {
        $return = true;

        $requiredFields = $this->getRequiredFields();
        foreach ($oParams[0] as $key => $value) {
            if (!in_array($key, $requiredFields) && !in_array($key, $this->_optionalFields)) {
                $return = false;
            }
        }/*
          foreach ($this->_optionalFields as $optionalField) {
          if (isset($oParams[0]->$optionalField)) {
          $return = true;
          }
          } */

        return $return;
    }

    /**
     * 
     * @return type
     */
    protected function getRequiredFields() {
        $requiredFields = array();

        foreach ($this->_requiredFields as $requiredField) {
            $strings = explode("|", $requiredField);
            foreach ($strings as $string) {
                $string = trim($string);
                $requiredFields[] = $string;
            }
        }

        return $requiredFields;
    }

    /**
     * 
     * @return type
     */
    protected function _userIsAllowedToExecute($receivedToken, $routeName) {
        $em = $this->getEntityManager();

        $token = $em->getRepository('User\Entity\AccessToken')
                ->findOneBy(array('value' => $receivedToken->getFieldValue()));

        if (empty($token)) {
            $return = false;
        } else {
            $profileHelper = $this->getServiceLocator()->get('ProfileHelper');
            $return = $profileHelper->userCanExecute($token->getUser(), $routeName);
        }

        //return $return;
        return true;
    }

    /**
     * Reads the Service Config .ini file and set up self::$_serviceConfig
     */
    private function _setServiceConfig() {
        if (is_null(self::$_serviceConfig)) {
            $reader = new Ini();
            self::$_serviceConfig = $reader->fromFile('config/servicesConfig.ini');
        }
    }

    /**
     * Decodes the current HTTP Request Body, which should be encoded in the JSON format
     * 
     * @return mixed
     */
    protected function _parseRequestBody() {
        return Json::decode($this->_request->getContent());
    }

    private function _setLoggedUser($receivedToken) {
        $em = $this->getEntityManager();

        $token = $em->getRepository('User\Entity\AccessToken')
                ->findOneBy(array('value' => $receivedToken->getFieldValue()));
        if (!empty($token)) {
            $this->_loggedUser = $token->getUser();
        }
    }

    public function getLoggedUser() {
        return $this->_loggedUser;
    }

    protected function setResponse(array $response) {
        $this->_response = $response;
    }

    protected function getJsonResponse() {
        return new JsonModel(array("result" => $this->_response));
    }

    public function getBearerToken() {
        return $this->_bearer->getFieldValue();
    }

}
