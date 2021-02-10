<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

if (!$object->xpdo) {
    return false;
}

$version_data = $object->xpdo->getVersionData();
$version = implode('.', [$version_data['version'], $version_data['major_version'], $version_data['minor_version']]);

if (!version_compare($version, '2.7', '>=')) {
    $object->xpdo->log(modX::LOG_LEVEL_ERROR, 'Invalid MODX version. Minimal supported version is 2.7.');

    return false;
}

return true;
