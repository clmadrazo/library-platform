<?php
/**
 * @category    Authentication
 * @package     Model
 * @subpackage  Factory
 */
namespace Authentication\Model\Factory;

use Authentication\Model\UserCredentials;

class UserCredentialsFactory
{
    public static function createFromArray(Array $credentials)
    {
        $email = null;
        $password = null;

        if (is_array($credentials) &&
            isset($credentials['username']) &&
            isset($credentials['password'])) {

            $email = $credentials['username'];
            $password = $credentials['password'];
        }

        return new UserCredentials($email, $password);
    }
}