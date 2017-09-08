<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Authentication\Controller\Authentication' => 'Authentication\Controller\AuthenticationController',
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'AuthenticationHelper' => 'Authentication\Model\Helper\AuthenticationHelper',
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/authentication/login[/]',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'login',
                    ),
                ),
            ),
            'forgot-password' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/authentication/forgot-password[/]',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'forgotPassword',
                    ),
                ),
            ),
            'valid-reset-password' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/authentication/validate-reset-password[/]',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'validateResetCode',
                    ),
                ),
            ),
            'set-password' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/authentication/set-password[/]',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'changePassword',
                    ),
                ),
            ),
            'refreshToken' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/authentication/refresh-token[/]',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'refreshToken',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);