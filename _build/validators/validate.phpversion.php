<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

if (!$object->xpdo) {
    return false;
}

if (!version_compare(PHP_VERSION, '7.1', '>=')) {
    $object->xpdo->log(modX::LOG_LEVEL_ERROR, 'Invalid php version. Minimal supported version – 7.1, because less versions not supported more by PHP core team. Details here: http://php.net/supported-versions.php');

    return false;
}

return true;
