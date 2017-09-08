<?php

namespace Authentication\Controller;

use Library\Mvc\Controller\RestfulController;
use Authentication\Model\UserCredentials;
use Zend\View\Model\JsonModel;

/**
 * This controller handles all authentication requests. In this first iteration
 * it only supports a very simple implementation but the idea is to, later on,
 * move forward into using a more secure and reliable mechanism like OAuth 2.0.
 * 
 * @todo This endpoints must implementing Transaction IDs (see Listing services)
 */
class AuthenticationController extends RestfulController {

    protected $_allowedMethod = "post";

    const ERR_COULDNT_RETRIEVE_USER_INFO = "Couldn't retrieve user info, please check the token";

    /**
     * @example
     *  [Request]
     *      POST /authentication/login
     *      Content-Type: application/json
     *      Accept: application/json
     *      {
     *          "username": "some-username",
     *          "password": "some-password"
     *      }
     * 
     * @return \Zend\Http\Message\Response
     */
    public function loginAction() {
        $request = $this->getRequest();
        $requestData = $this->processBodyContent($request);

        $return = $this->doLogin($requestData);

        return $return;
    }

    public function doLogin($data, $isSocial = false) {
        $return = null;
        $response = $this->getResponse();
        $userCredentials = new UserCredentials();
        $userCredentials->exchangeArray($data);

        // Filter & validate credentials (fail early).
        if ($userCredentials->isValid()) {
            $authHelper = $this->getServiceLocator()->get('AuthenticationHelper');
            if (!$isSocial) {
                // Authenticate user credentials only if it's a login service (not social)
                $userId = $authHelper->authenticate($userCredentials);
            } else {
                $userId = $data[0]['userId'];
            }

            if ($userId) {
                $headers = $response->getHeaders();
                $profileHelper = $this->getServiceLocator()->get('ProfileHelper');
                $user = $profileHelper->getUser($userId);
                $headers->addHeaderLine('X-User-Id', $userId);

                $headers->addHeaderLine('User-Name', htmlentities($user->getName() . ' ' . $user->getLastname()));
                $headers->addHeaderLine('User-Email', $user->getEmail());
                $headers->addHeaderLine('User-Created', $user->getCreated());
                $userRoles = $user->getRoles();
                $tempRolesIds = array();
                $tempRolesNames = array();
                foreach ($userRoles as $userRole) {
                    $id = $userRole->getId();
                    if (!in_array($id, $tempRolesIds)) {
                        $tempRolesIds[] = $id;
                        $tempRolesNames[] = $userRole->getTitle();
                    }
                }
                $userRolesString = implode(',', $tempRolesIds);
                $userRolesNamesString = implode(',', $tempRolesNames);

                $headers->addHeaderLine('User-Roles', $userRolesString);
                $headers->addHeaderLine('User-Roles-Names', $userRolesNamesString);
                $headers->addHeaderLine('Bearer-Token', $profileHelper->generateToken($user));
                $headers->addHeaderLine('Refresh-Token', $profileHelper->generateToken($user, true));
                $return = $response->setStatusCode(200);
            } else {
                $return = $response->setStatusCode(404);
            }
        } else {
            $response->setStatusCode(400);
            $errorMessages = array(
                'errors' => $userCredentials->getErrorMessages(),
            );
            $return = new JsonModel($errorMessages);
        }

        return $return;
    }

    public function refreshTokenAction() {
        $response = $this->getResponse();
        $requestData = $this->processBodyContent($this->getRequest());
        $em = $this->getEntityManager();

        $token = $em->getRepository('User\Entity\AccessToken')
                ->findOneBy(array('refresh' => $requestData[0]['refreshToken']));

        if (empty($token)) {
            $response->setStatusCode(400);
            $return = new JsonModel(array('errors' => parent::PROCESS_REQUEST_ERROR));
        } else {
            $profileHelper = $this->getServiceLocator()->get('ProfileHelper');
            $token->setValue($profileHelper->generateToken($token->getUser()));
            $headers = $response->getHeaders();
            $headers->addHeaderLine('X-User-Id', $token->getUser()->getId());
            $headers->addHeaderLine('User-Email', $token->getUser()->getEmail());
            $headers->addHeaderLine('Bearer-Token', $profileHelper->generateToken($token->getUser()));
            $headers->addHeaderLine('Refresh-Token', $profileHelper->generateToken($token->getUser(), 1));

            $return = $response->setStatusCode(200);
        }

        return $return;
    }

    /**
     * @example
     *  [Request]
     *      POST /authentication/forgot-password
     *      Content-Type: application/json
     *      {
     *          "email": "some-email"
     *      }
     */
    public function forgotPasswordAction() {
        $em = $this->getEntityManager();
        $requestData = $this->processBodyContent($this->getRequest());

        $profileHelper = $this->getServiceLocator()->get('ProfileHelper');
        $user = $profileHelper->getUserByEmail($requestData[0]['email']);

        if (!empty($user)) {
            $authHelper = $this->getServiceLocator()->get('AuthenticationHelper');
            $resetCode = $authHelper->generateResetPasswordCode($requestData[0]['email']);
            $user->setResetPasswordCode($resetCode);
            $expiration = new \DateTime('now + 15 minutes');
            $user->setResetPasswordCodeExpiration($expiration);
            $em->persist($user);
            $em->flush();

            $text = 'Olá, <br />
                     Este é o código para recuperar a sua senha: ' . $resetCode . '. Você tem até 15 minutos para alterar sua senha com este código.';
                $subject = 'Código de recuperação de senha';

            $message = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                            <title>Library Platform - Reset Code</title>
                        </head>
                        <body>
                           ' . $text . '
                        </body>
                    </html>
        ';


            // $url = 'http api.sendgrid'; 
            // $user = 'username';
            // $pass = 'password';
            // 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

            $template = '{
                        "filters": {
                          "templates": {
                            "settings": {
                              "enable": 1,
                              "template_id": "b3027402-0ef2-4c3c-912c-e8e10d206344"
                            }
                          }
                        }
                     }';

            $params = array(
                'api_user' => $user,
                'api_key' => $pass,
                'to' => $requestData[0]['email'],
                'subject' => $subject,
                'html' => $message,
                'text' => $message,
                'from' => 'email',
                'x-smtpapi' => $template,
                'body' => 'este é o body',
            );


            $request = $url . 'api/mail.send.json';

            // Generate curl request
            $session = curl_init($request);
            // Tell curl to use HTTP POST


            curl_setopt($session, CURLOPT_POST, true);
            // Tell curl that this is the body of the POST
            curl_setopt($session, CURLOPT_POSTFIELDS, $params);
            // Tell curl not to return headers, but do return the response
            curl_setopt($session, CURLOPT_HEADER, false);
            // Tell PHP not to use SSLv3 (instead opting for TLS)
            curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

            // obtain response
            $response = curl_exec($session);
            curl_close($session);

            $return = array("resetCode" => "OK");
        } else {
            $this->getResponse()->setStatusCode(404);
            $return = array("errors" => "Email not registered");
        }

        return new JsonModel(array("result" => $return));
    }

    /**
     * @example
     *  [Request]
     *      POST /authentication/validate-reset-password
     *      Content-Type: application/json
     *      {
     *          "email": "some-email",
     *          "resetCode": "some-code"
     *      }
     */
    public function validateResetCodeAction() {
        $response = $this->getResponse();
        $requestData = $this->processBodyContent($this->getRequest());

        $authHelper = $this->getServiceLocator()->get('AuthenticationHelper');
        if ($authHelper->isValidResetCode($requestData[0]['email'], $requestData[0]['resetCode'])) {
            $response->setStatusCode(200);
        } else {
            $response->setStatusCode(404);
        }

        return $response;
    }

    /**
     * @example
     *  [Request]
     *      POST /authentication/set-password
     *      Content-Type: application/json
     *      Accept: application/json
     *      {
     *          "email": "some-email",
     *          "password": "password"
     *      }
     *
     * @return \Zend\Http\Message\Response
     */
    public function changePasswordAction() {
        $response = $this->getResponse();
        $requestData = $this->processBodyContent($this->getRequest());
        $authHelper = $this->getServiceLocator()->get('AuthenticationHelper');

        $userEntity = new UserCredentials();
        $userEntity->exchangeArray($requestData);
        if ($userEntity->isValid()) {
            $email = $userEntity->getEmail();
            $password = stripslashes($userEntity->getPassword());
            if ($authHelper->setPassword($email, $password)) {
                $response->setStatusCode(200);
            }
        } else {
            $response->setStatusCode(400);
        }

        return $response;
    }

}
