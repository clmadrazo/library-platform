<?php

namespace User;

return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\UserGet'                   => 'User\Controller\UserGetController',
            'User\Controller\UserUpdate'                => 'User\Controller\UserUpdateController',
            'User\Controller\UserRoleUpdate'            => 'User\Controller\UserRoleUpdateController',
            'User\Controller\UserSkillGet'              => 'User\Controller\UserSkillGetController',
            'User\Controller\UserSendInvitations'       => 'User\Controller\UserSendInvitationsController',
            'User\Controller\ListUser'                  => 'User\Controller\ListUserController',
            'Profile\Controller\GetUser'                => 'Profile\Controller\GetUserController',
            'User\Controller\JoinTeam'                  => 'User\Controller\JoinTeamController',
            'User\Controller\InactivateUser'            => 'User\Controller\InactivateUserController',
            'User\Controller\DeleteUser'                => 'User\Controller\DeleteUserController',
            'User\Controller\GetUserNotifications'      => 'User\Controller\GetUserNotificationsController',
            'User\Controller\GetUserBalance'            => 'User\Controller\GetUserBalanceController',
            'User\Controller\SetUserNotificationsRead'  => 'User\Controller\SetUserNotificationsReadController',
            'User\Controller\GetAssignedPost'           => 'User\Controller\GetAssignedPostController',
            'User\Controller\GetPostAssignment'         => 'User\Controller\GetPostAssignmentController',
            'User\Controller\DeleteUserInvitation'      => 'User\Controller\DeleteUserInvitationController',
            'User\Controller\ImportViviliaUsers'        => 'User\Controller\ImportViviliaUsersController',
            'User\Controller\ImportDevUsers'            => 'User\Controller\ImportDevUsersController',
            'User\Controller\ApproveCreditRequest'      => 'User\Controller\ApproveCreditRequestController',
            'User\Controller\RequestCredit'             => 'User\Controller\RequestCreditController',
            'User\Controller\RequestPayment'            => 'User\Controller\RequestPaymentController',
            'Listing\Controller\UserCreditRequest'      => 'Listing\Controller\UserCreditRequestController',
            'User\Controller\HoldPayment'               => 'User\Controller\HoldPaymentController',
            'User\Controller\GetUserTopics'             => 'User\Controller\GetUserTopicsController',
            'User\Controller\GetUserPostStatus'         => 'User\Controller\GetUserPostStatusController',
            'User\Controller\GetUserProjects'           => 'User\Controller\GetUserProjectsController',
            'User\Controller\GetUserBalanceMovements'   => 'User\Controller\GetUserBalanceMovementsController',
            'User\Controller\ChangeUserStatus'          => 'User\Controller\ChangeUserStatusController',
            'User\Controller\CheckEmail'                => 'User\Controller\CheckEmailController',
            'User\Controller\ExportUser'                => 'User\Controller\ExportUserController',
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
            'importViviliaUsers' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/users/import-vivilia-users[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\ImportViviliaUsers',
                        'action' => 'index',
                    ),
                ),
            ),
            'importDevUsers' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/users/import-dev-users[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\ImportDevUsers',
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
            'joinTeam' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/join-team[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\JoinTeam',
                        'action' => 'index',
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
            'getUser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/get/:userId[/]',
                    'defaults' => array(
                        'controller' => 'Profile\Controller\GetUser',
                        'action' => 'index',
                    ),
                ),
            ),
            'getUserByProfileURL' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/get/public-profile/:profileURI/:language[/]',
                    'defaults' => array(
                        'controller' => 'Profile\Controller\GetUser',
                        'action' => 'getByProfileURL',
                    ),
                ),
            ),
            'getUserNotifications' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/notifications',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetUserNotifications',
                        'action' => 'index',
                    ),
                ),
            ),
            'getUserBalance' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/balance/get',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetUserBalance',
                        'action' => 'index',
                    ),
                ),
            ),
            'requestCredit' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/credit/request',
                    'defaults' => array(
                        'controller' => 'User\Controller\RequestCredit',
                        'action' => 'index',
                    ),
                ),
            ),
            'listRequestCredit' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/credit/list',
                    'defaults' => array(
                        'controller' => 'Listing\Controller\UserCreditRequest',
                        'action' => 'index',
                    ),
                ),
            ),
            'approveCreditRequest' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/credit/approve',
                    'defaults' => array(
                        'controller' => 'User\Controller\ApproveCreditRequest',
                        'action' => 'index',
                    ),
                ),
            ),
            'getAssignedPost' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/get/assigned-post/:userId[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetAssignedPost',
                        'action' => 'index',
                    ),
                ),
            ),
            'getPostAssignment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/get/post-assignment/:postId[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetPostAssignment',
                        'action' => 'index',
                    ),
                ),
            ),
            'requestPayment' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/payment/request',
                    'defaults' => array(
                        'controller' => 'User\Controller\RequestPayment',
                        'action' => 'index',
                    ),
                ),
            ),
            'holdPayment' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/payment/hold',
                    'defaults' => array(
                        'controller' => 'User\Controller\HoldPayment',
                        'action' => 'index',
                    ),
                ),
            ),
            'getUserTopics' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/topics/get',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetUserTopics',
                        'action' => 'index',
                    ),
                ),
            ),
            'getUserPostStatus' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/post-status/get',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetUserPostStatus',
                        'action' => 'index',
                    ),
                ),
            ),
            'getUserProjects' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/projects/get',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetUserProjects',
                        'action' => 'index',
                    ),
                ),
            ),
            'getUserBalanceMovements' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/balance/movements/get',
                    'defaults' => array(
                        'controller' => 'User\Controller\GetUserBalanceMovements',
                        'action' => 'index',
                    ),
                ),
            ),
            'setUserNotificationsRead' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/notifications/read',
                    'defaults' => array(
                        'controller' => 'User\Controller\SetUserNotificationsRead',
                        'action' => 'index',
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
            'userSendInvitations' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/send-invitations[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\UserSendInvitations',
                        'action' => 'index',
                    ),
                ),
            ),
            'deleteUserInvitation' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/delete-invitation[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\DeleteUserInvitation',
                        'action' => 'index',
                    ),
                ),
            ),
            'search-skill' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/skill/search/',
                    'defaults' => array(
                        'controller' => 'User\Controller\UserSkillGet',
                        'action' => 'search',
                    ),
                ),
            ),
            'exportUser' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/users/list/export[/]',
                    'defaults' => array(
                        'controller' => 'User\Controller\ExportUser',
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
                'child_routes' => array(
                    'articleNotifications' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/articleNotifications[/]',
                        ),
                        'may_terminate' => false,
                        'child_routes' => array(
                            'update' => array(
                                'type' => 'method',
                                'options' => array(
                                    'verb' => 'put',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\UserUpdate',
                                        'action' => 'updateArticleNotifications',
                                    ),
                                ),
                            ),
                            'get' => array(
                                'type' => 'method',
                                'options' => array(
                                    'verb' => 'get',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\UserGet',
                                        'action' => 'getArticleNotifications',
                                    ),
                                ),
                            ),
                        ),
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
