<?php

use Symfony\Component\HttpFoundation\Response;
use Silex\Provider;

$app['env'] = isset($app['env']) ? $app['env'] : 'prod';

$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());
$app->register(new Provider\HttpFragmentServiceProvider());
$app->register(new Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ .'/../templates',
    'twig.options' => array(
        //'cache' => __DIR__.'/../var/cache/twig'
    ),
));

//profiler
if ($app['debug']) {
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__ .'/../var/cache/profiler',
        'profiler.mount_prefix' => '/_profiler',
    ));
}

//Logging the error logs
$app->register(new Provider\MonologServiceProvider(), array(
    'monolog.logfile'    => __DIR__.'/../var/logs/error.log',
    'monolog.level'      => Monolog\Logger::ERROR,
    'monolog.name'       => 'app'
));

//handling errors
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }
    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );
    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

//routes
require_once __DIR__ . '/routes.php';
