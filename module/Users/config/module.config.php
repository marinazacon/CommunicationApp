<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\Index' =>
                'Users\Controller\IndexController',
            'Users\Controller\Register' =>
                'Users\Controller\RegisterController',
            'Users\Controller\Login' =>
                'Users\Controller\LoginController',
            'Users\Controller\UserManager' =>
                'Users\Controller\UserManagerController',
            'Users\Controller\UploadManager' =>
                'Users\Controller\UploadManagerController',
            'Users\Controller\GroupChat' =>
                'Users\Controller\GroupChatController',
            'Users\Controller\Mail' =>
                'Users\Controller\MailController',
            'Users\Controller\MediaManager' =>
                'Users\Controller\MediaManagerController',
            'Users\Controller\Search' =>
                'Users\Controller\SearchController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'users' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/users',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'register' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'Register',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'process' => array(
                                'type'      => 'Segment',
                                'options'   => array(
                                    'route' => '/process',
                                    'defaults' => array(
                                        'controller' => 'Register',
                                        'action'     => 'process',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'confirm' => array(
                                        'type'      => 'Segment',
                                        'options'   => array(
                                            'route' => '/confirm',
                                            'defaults' => array(
                                                'controller' => 'Register',
                                                'action'     => 'confirm',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'login' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/login',
                            'defaults' => array(
                                'controller' => 'Login',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'process' => array(
                                'type'      => 'Segment',
                                'options'   => array(
                                    'route' => '/process',
                                    'defaults' => array(
                                        'controller' => 'Login',
                                        'action'     => 'process',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'confirm' => array(
                                        'type'      => 'Segment',
                                        'options'   => array(
                                            'route' => '/confirm',
                                            'defaults' => array(
                                                'controller' => 'Login',
                                                'action'     => 'confirm',
                                            ),
                                        ),
                                    ),
                                    'error' => array(
                                        'type'      => 'Segment',
                                        'options'   => array(
                                            'route' => '/error',
                                            'defaults' => array(
                                                'controller' => 'Login',
                                                'action'     => 'error',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'user-manager' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/user-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'      => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'UserManager',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'upload-manager' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/upload-manager[/:action[/:id[/:user_id]]]',
                            'defaults' => array(
                                'controller' => 'UploadManager',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'group-chat' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/group-chat[/:action[/:id]]',
                            'constraints' => array(
                                'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'      => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'GroupChat',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'mail' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route' => '/mail[/:action[/:id]]',
                            'constraints' => array(
                                'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'      => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Mail',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'media-manager' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route'         => '/media[/:action[/:id[/:subaction]]]',
                            'constraints'   => array(
                                'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'        => '[a-zA-Z0-9_-]*',
                                'subaction' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\MediaManager',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'search' => array(
                        'type'      => 'Segment',
                        'options'   => array(
                            'route'         => '/search[/:action]',
                            'constraints'   => array(
                                'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\Search',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'users' => __DIR__ . '/../view',
            ),
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'        => __DIR__ . '/../view/layout/layout.phtml',
            'layout/myaccount'        => __DIR__ . '/../view/layout/myaccount.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
    ),
    'module_config' => array(
        'upload_location' => __DIR__ . '/../data/uploads/',
        'client_secret' => __DIR__ . '/../../../client_secret.json',
        'search_index' => __DIR__ . '/../data/search_index',
    ),
);
