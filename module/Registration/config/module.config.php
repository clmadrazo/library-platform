<?php
namespace Registration;

return array(
    'controllers' => array(
        'invokables' => array(
            'Registration\Controller\Registration' => 'Registration\Controller\RegistrationController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'registration' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/registration[/]',
                    'defaults' => array(
                        'controller' => 'Registration\Controller\Registration',
                        'action' => 'index',
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
