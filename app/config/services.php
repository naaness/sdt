<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
$di->set('config', $config);
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);



/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

                $volt = new VoltEngine($view, $di);

                $compiler = $volt->getCompiler();

//                This binds the function name 'shuffle' in Volt to the PHP function 'str_shuffle'
                $compiler->addFunction('str_replace', 'str_replace');
                // $compiler->addFunction('dump', 'print_r');
                $compiler->addFunction('explode', 'explode');
                $compiler->addFunction('count', 'count');

                $volt->setOptions(array(
                    'compiledPath' => $config->application->cacheDir,
                    'compiledSeparator' => '_'
                ));

                return $volt;
            },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset//esta es la clave
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

/**
 * podemos añadir clases personalizadas a los mensajes flash de esta forma
 */
$di->set(
    'flash',
    function () {
        return new Phalcon\Flash\Session(array(
            'error' => 'alert alert-dismissable alert-danger',
            'success' => 'alert alert-dismissable alert-success',
            'notice' => 'alert alert-dismissable alert-info',
        ));
    }
);

/**
 * Registramos el gestor de eventos
 */
$di->set('dispatcher', function() use ($di)
{

    $eventsManager = $di->getShared('eventsManager');
    $roles = new Roles($di);
    /**
     * Escuchamos eventos en el componente dispatcher usando el plugin Roles
     */
    $eventsManager->attach('dispatch', $roles);

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

/**
 * Mail service uses Gmail
 */
$di->set('mail', function(){
    return new Mail();
});

/**
 * Multiples formularios
 */
$di->set('forms', function() {
    return new \Phalcon\Forms\Manager();
});

/**
 * Sdt
 */
$di->set('sdt', function(){
    return new Sdt();
});
/**
 * Sdt
 */
$di->set('jcrypt', function(){
    return new JCryptionSdt();
});

/**
 * Sdt
 */
$di->set('sdtRuler', function(){
    return new SdtRule();
});

/**
 * Funciones basicas para conectar con google calendar
 */
$di->set('gcalendar', function(){
    return new GCalendar();
});

/**
 * Componente personalizado de autoenticacion
 */
$di->set('auth', function () {
    return new Auth();
});

// Cookies
$di->set( 'cookies', function () {
    $cookies = new Phalcon\Http\Response\Cookies();
    $cookies->useEncryption( false );
    return $cookies;
});