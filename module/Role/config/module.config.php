<?php
namespace Role;

return array(
    'controllers' => array(
        'invokables' => array(
            'Role\Controller\Role' => 'Role\Controller\RoleController',
            'Role\Controller\PostRole' => 'Role\Controller\PostRoleController',
        )
    )
    ,
    'service_manager' => array(
        'invokables' => array(
            'RoleHelper' => 'Role\Model\Helper\RoleHelper',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'addRole' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/role/add[/]',
                    'defaults' => array(
                        'controller' => 'Role\Controller\PostRole',
                        'action' => 'add'
                    ),
                ),
            ),
            'deleteRole' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/role/delete[/]',
                    'defaults' => array(
                        'controller' => 'Role\Controller\PostRole',
                        'action' => 'delete'
                    ),
                ),
            ),
            'editRole' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/role/edit[/]',
                    'defaults' => array(
                        'controller' => 'Role\Controller\PostRole',
                        'action' => 'edit'
                    ),
                ),
            ),
            'role' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/role[/]',
                    'defaults' => array(
                        'controller' => 'Role\Controller\Role',
                        'action' => 'index'
                    ),
                ),
            ),

            /*
            'role' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/role[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Role\Controller\Role',
                        'action'     => 'index',
                    ),
                ),
            ),
            */
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
    )
);