<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

define('MODX_API_MODE', true);

require_once __DIR__ . '/../../../index.php';

$modx->initialize('mgr');

$modx->setLogLevel(xPDO::LOG_LEVEL_ERROR);
$modx->setLogTarget();

$modx->runProcessor('workspace/packages/scanlocal');
$answer = $modx->runProcessor('workspace/packages/install',
    ['signature' => 'msdeliveryprops-0.3.4-stable']
);

$response = $answer->getResponse();

echo $response['message'], PHP_EOL;

$modx->getCacheManager()->refresh(['system_settings' => []]);
$modx->reloadConfig();
