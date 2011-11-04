<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bip\Service as BipService;
use Bip\Repository as BipRepository;

require_once __DIR__ . '/../silex.phar';

$app = new Silex\Application();
$app['debug'] = true;
$app['autoloader']->registerNamespace('Bip', __DIR__ . '/../src');
$app['db.driver'] = 'sqlite';
$app['db.path'] = __DIR__ . '/../bips.db';

$app['bip.service'] = function() use ($app) {
    $bipService = new BipService();
    return $bipService->setContainer($app);
};

$app['bip.repository'] = function() use ($app) {
    $repository = new BipRepository();
    return $repository->setContainer($app);
};

$app->get('/bips/{group}', function (Request $request, $group) use ($app) {
    return new Response(
        json_encode($app['bip.service']->getBipsByGroup($group)),
        200,
        array('Content-Type' => 'application/json')
    );
});

$app->post('/bips', function (Request $request) use ($app) {
    $postData = json_decode(stripslashes(file_get_contents('php://input')));

    $bipService = $app['bip.service'];
    $bipService->setPosition((array)$postData);

    return new Response(
        $postData->Person,
        200,
        array('Content-Type' => 'application/json')
    );
});

$app->get('/db-devel', function () use ($app) {
    $app['bip.repository']->devel();
});

$app->get('/', function () use ($app) {
    return new Response('hi');
});

$app->run();