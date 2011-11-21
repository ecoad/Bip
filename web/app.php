<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bip\Service as BipService;
use Bip\Repository\BipRepository;
use Bip\EntityMapper\BipEntityMapper;
use Bip\Entity\Bip as BipEntity;

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

$app['bip.repository.bip'] = function() use ($app) {
    return new BipRepository($app);
};

$app['bip.entityMapper.bip'] = function() use ($app) {
    return new BipEntityMapper($app);
};

$app['bip.entity.bip'] = function() use ($app) {
    return new BipEntity();
};

$app->get('/bips/{group}', function (Request $request, $group) use ($app) {
    $bips = $app['bip.service']->getBipsByGroup($group); 
    
    return new Response(
        json_encode($app['bip.service']->getBipsAsPlainObjects($bips)),
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

//TODO: Remove
$app->get('/db-devel', function () use ($app) {
    $app['bip.repository']->devel();
});

$app->get('/', function () use ($app) {
    return new Response('hi');
});

$app->run();