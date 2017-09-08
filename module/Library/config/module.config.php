<?php
namespace Library;

return array(
    'controllers' => array(
        'invokables' => array(
            'Library\Controller\Testing'            => 'Library\Controller\TestingController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'testing' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/library/testing[/]',
                    'defaults' => array(
                        'controller' => 'Library\Controller\Testing',
                        'action' => 'index'
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