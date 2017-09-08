<?php

namespace User;

return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\UserGet'                   => 'User\Controller\UserGetController',
            'User\Controller\UserUpdate'                => 'User\Controller\UserUpdateController',
            'User\Controller\UserRoleUpdate'            => 'User\Controller\UserRoleUpdateController',
            'User\Controller\ListUser'                  => 'User\Controller\ListUserController',
            'User\Controller\InactivateUser'            => 'User\Controller\InactivateUserController',
            'User\Controller\DeleteUser'                => 'User\Controller\DeleteUserController',
            'User\Controller\ChangeUserStatus'          => 'User\Controller\ChangeUserStatusController',
            'User\Controller\CheckEmail'                => 'User\Controller\CheckEmailController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'QueryPaginator' => 'Library\Mvc\Controller\Plugin\Doctrine\QueryPaginator',
        )
    ),
    'router' => array(
        'routes' => array(
            'deleteUser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/inactivateuser[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\DeleteUser',
                        'action' => 'index',
                    ),
                ),
            ),
            'inactivateUser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/inactivateuser[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\InactivateUser',
                        'action' => 'index',
                    ),
                ),
            ),
            'checkEmail' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/check-email-exists[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\CheckEmail',
                        'action' => 'index',
                    ),
                ),
            ),
            'activateUser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/activateUser[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\UserUpdate',
                        'action' => 'activeUser',
                    ),
                ),
            ),
            'changeUserStatus' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/change-status[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\ChangeUSerStatus',
                        'action' => 'index',
                    ),
                ),
            ),
            'updateUser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/userupdate/:userId[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\UserRoleUpdate',
                        'action' => 'index',
                    ),
                ),
            ),
            'search-user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/search/',
                    'defaults' => array(
                        'controller' => 'User\Controller\UserGet',
                        'action' => 'search',
                    ),
                ),
            ),
            'listUser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/list[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\ListUser',
                        'action' => 'index',
                    ),
                ),
            ),
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/:userId',
                    'constraints' => array(
                        'publisherId' => '[0-9]+',
                    ),
                ),
                'may_terminate' => false,
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
);
