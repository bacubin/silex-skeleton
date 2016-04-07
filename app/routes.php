<?php
$controllers = $app['controllers_factory'];
$app->mount('/', new Demo\Controller\DefaultController());
$app->mount('/api', new Demo\Controller\Api\DefaultController());


