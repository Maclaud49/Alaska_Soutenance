<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;



// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' =>__DIR__.'/../views'
));

// Register Twig services
$app['twig'] = $app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
});

// Register Asset services ({{ asset('/css/alaska.css') }})
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));
// Register session services
$app->register(new Silex\Provider\SessionServiceProvider());

//Register authentification and authorization services
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/connexion', 'check_path' => '/login_check'),
            'remember_me' => array(
                'key' => MD5('secretAlaska'),
            ),
            'users' => function () use ($app) {
                return new Alaska\Manager\UserManager($app['db']);
            },
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
    'security.access_rules' => array(
        array('^/admin', 'ROLE_ADMIN'),
    ),
));

//Register remember me services
$app->register(new Silex\Provider\RememberMeServiceProvider());



//Register form services
$app->register(new Silex\Provider\FormServiceProvider());
//Register validator data services
$app->register(new Silex\Provider\ValidatorServiceProvider());

//Register translation services
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());


//Register log services
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/alaska.log',
    'monolog.name' => 'Alaska',
    'monolog.level' => $app['monolog.level']
));

//Register mail services
$app->register(new Silex\Provider\SwiftmailerServiceProvider());


//Configure Swiftmail to use SMTP. (does not work online)
$app->register(new Silex\Provider\SwiftmailerServiceProvider(),  array(
        'swiftmailer.options' => array(
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'username' => "billet.simple.alaska@gmail.com",
        'password' => "Alaska2017&",
        'encryption' => 'ssl',
        'auth_mode' => 'login'),
));

// Register services
$app['manager.article'] = function ($app) {
    return new Alaska\Manager\ArticleManager($app['db']);
};
$app['manager.user'] = function ($app) {
    return new Alaska\Manager\UserManager($app['db']);
};
$app['manager.comment'] = function ($app) {
        $commentManager = new Alaska\Manager\CommentManager($app['db']);
        $commentManager->setArticleManager($app['manager.article']);
        $commentManager->setUserManager($app['manager.user']);
        return $commentManager;
};

$app['manager.commentReported'] = function ($app) {
    $commentReportedManager = new Alaska\Manager\CommentReportedManager($app['db']);
    $commentReportedManager->setCommentManager($app['manager.comment']);
    return $commentReportedManager;
};

// Register error handler

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Accès refusé.';
            break;
        case 404:
            $message = 'La page demandée n\'existe pas.';
            break;
        default:
            $message = 'Une erreur est survenue';
    }
    $articlesVisible = $app['manager.article']->findAllVisible();
    return $app['twig']->render('error.html.twig', array('message' => $message,'articlesVisible' => $articlesVisible,));
});

/*$app->error(function (\Swift_TransportException $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Accès refusé.';
            break;
        default:
            $message = 'Le message n\'a pas pu être envoyé. Problème technique qui sera résolu très rapidement.';
    }
    return $app['twig']->render('error.html.twig', array('message' => $message));
});*/


