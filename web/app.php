<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bip\Service as BipService;

require_once __DIR__ . '/../silex.phar';

$app = new Silex\Application();
$app['debug'] = true;
$app['autoloader']->registerNamespace('Bip', __DIR__ . '/../src');

$app['bip.service'] = function () use ($app) {
    $bipService = new BipService();
    $bipService->setContainer($app);
    return $bipService;
};

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../app.db',
    ),
    'db.dbal.class_path'    => __DIR__ . '/../vendor/doctrine/dbal/lib',
    'db.common.class_path'  => __DIR__ . '/../vendor/doctrine/common/lib'
));

$app->get('/update', function (Request $request) use ($app) {
    $bipService = $app['bip.service'];
    $bipService->updatePosition($request);
    
    $responseData = new stdClass();
    $responseData->bips = $bipService->getBips($request);

    return new Response(
        json_encode($responseData),
        200,
        array('Content-Type' => 'application/json')
    );
});

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
});

$app->run();