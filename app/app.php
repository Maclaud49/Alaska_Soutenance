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
    'twig.path' => __DIR__.'/../views',
));


$app['twig'] = $app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
});

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
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
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/alaska.log',
    'monolog.name' => 'Alaska',
    'monolog.level' => $app['monolog.level']
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

/*$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Accès refusé.';
            break;
        case 404:
            $message = 'La page demandée n\'existe pas.';
            break;
        default:
            $message = 'Une erreur inconnue est survenue';
    }
    return $app['twig']->render('error.html.twig', array('message' => $message));
});*/


