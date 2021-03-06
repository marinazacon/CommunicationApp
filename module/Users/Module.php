<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

use Users\Model\User;
use Users\Model\UserTable;
use Users\Model\Upload;
use Users\Model\UploadTable;
use Users\Model\ChatMessages;
use Users\Model\ChatMessagesTable;
use Users\Model\ImageUpload;
use Users\Model\ImageUploadTable;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /*
    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    */

    public function onBootstrap($e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $sharedEventManager = $eventManager->getSharedManager();

        // The shared event manager
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function($e) {
            $controller = $e->getTarget();
            // The controller which is dispatched
            $controllerName = $controller->getEvent()->getRouteMatch()->getParam('controller');
            if (in_array($controllerName,
                array(
                    'Users\Controller\Index',
                    'Users\Controller\Register',
                    'Users\Controller\Login'
                )))
            {
                $controller->layout('layout/layout');
            }
            if (in_array($controllerName,
                array(
                    'Users\Controller\GroupChat',
                    'Users\Controller\Mail',
                    'Users\Controller\UserManager',
                    'Users\Controller\UploadManager',
                    'Users\Controller\MediaManager',
                    'Users\Controller\Search'
                )))
            {
                $controller->layout('layout/myaccount');
            }
        });

    }

    public function getServiceConfig()
    {
        return array(
            'abstract_factories' => array(),
            'aliases' => array(),
            'factories' => array(
// DB - User table
                'UserTable' => function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null,
                        $resultSetPrototype);
                },
// DB - Upload table
                'UploadTable' => function($sm) {
                    $tableGateway = $sm->get('UploadTableGateway');
                    $uploadSharingTableGateway = $sm->get('UploadSharingTableGateway');
                    $table = new UploadTable($tableGateway, $uploadSharingTableGateway);
                    return $table;
                },
                'UploadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Upload());
                    return new TableGateway('uploads', $dbAdapter, null,
                        $resultSetPrototype);
                },
// DB - Uploads sharing table gateway
                'UploadSharingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('uploads_sharing', $dbAdapter);
                },
// DB - Chat Messages table
                'ChatMessagesTable' => function($sm) {
                    $tableGateway = $sm->get('ChatMessagesTableGateway');
                    $table = new ChatMessagesTable($tableGateway);
                    return $table;
                },
                'ChatMessagesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ChatMessages());
                    return new TableGateway('chat_messages', $dbAdapter, null,
                        $resultSetPrototype);
                },
// DB - Image Upload table
                'ImageUploadTable' => function($sm) {
                    $tableGateway = $sm->get('ImageUploadTableGateway');
                    $table = new ImageUploadTable($tableGateway);
                    return $table;
                },
                'ImageUploadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ImageUpload());
                    return new TableGateway('image_uploads', $dbAdapter, null,
                        $resultSetPrototype);
                },
// FORMS
                'LoginForm' => function ($sm) {
                    $form = new \Users\Form\LoginForm();
                    $form->setInputFilter($sm->get('LoginFilter'));
                    return $form;
                },
                'RegisterForm' => function ($sm) {
                    $form = new \Users\Form\RegisterForm();
                    $form->setInputFilter($sm->get('RegisterFilter'));
                    return $form;
                },
                'UserEditForm' => function ($sm) {
                    $form = new \Users\Form\UserEditForm();
                    $form->setInputFilter($sm->get('UserEditFilter'));
                    return $form;
                },
                'UserDeleteForm' => function ($sm) {
                    $form = new \Users\Form\UserDeleteForm();
                    return $form;
                },
                'UserAddForm' => function ($sm) {
                    $form = new \Users\Form\UserAddForm();
                    $form->setInputFilter($sm->get('UserAddFilter'));
                    return $form;
                },
                'UploadDeleteForm' => function ($sm) {
                    $form = new \Users\Form\UploadDeleteForm();
                    return $form;
                },
                'UploadForm' => function ($sm) {
                    $form = new \Users\Form\UploadForm();
                    $form->setInputFilter($sm->get('UploadFilter'));
                    return $form;
                },
                'AddShareForm' => function ($sm) {
                    $form = new \Users\Form\AddShareForm();
                    return $form;
                },
                'MailForm' => function ($sm) {
                    $form = new \Users\Form\MailForm();
                    $form->setInputFilter($sm->get('MailFilter'));
                    return $form;
                },
                'ImageUploadForm' => function ($sm) {
                    $form = new \Users\Form\ImageUploadForm();
                    $form->setInputFilter($sm->get('ImageUploadFilter'));
                    return $form;
                },
                'ImageUploadDeleteForm' => function ($sm) {
                    $form = new \Users\Form\ImageUploadDeleteForm();
                    return $form;
                },
                'SearchForm' => function ($sm) {
                    $form = new \Users\Form\SearchForm();
                    return $form;
                },
// FILTERS
                'LoginFilter' => function ($sm) {
                    return new \Users\Form\LoginFilter();
                },
                'RegisterFilter' => function ($sm) {
                    return new \Users\Form\RegisterFilter();
                },
                'UserEditFilter' => function ($sm) {
                    return new \Users\Form\UserEditFilter();
                },
                'UserAddFilter' => function ($sm) {
                    return new \Users\Form\UserAddFilter();
                },
                'UploadFilter' => function ($sm) {
                    return new \Users\Form\UploadFilter();
                },
                'MailFilter' => function ($sm) {
                    return new \Users\Form\MailFilter();
                },
                'ImageUploadFilter' => function ($sm) {
                    return new \Users\Form\ImageUploadFilter();
                },
// Authentication
                'AuthService' => function($sm) {
                    $dbTableAuthAdapter = $sm->get('AuthAdapter');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },
                'AuthAdapter' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new DbTableAuthAdapter($dbAdapter,'user','email','password', 'MD5(?)');
                },
            ),
            'invokables' => array(),
            'services' => array(),
            'shared' => array(),
        );
    }
}
