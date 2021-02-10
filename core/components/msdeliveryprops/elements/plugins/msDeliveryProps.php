<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

require_once MODX_CORE_PATH . 'components/msdeliveryprops/ConfigurableDeliveryHandler.class.php';

switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':

        switch ($_GET['a']) {
            case 'system/settings':
                ConfigurableDeliveryHandler::loadExtraJs($modx, [
                    'status.combo.js',
                    'resource.combo.js'
                ]); break;
            case 'mgr/settings':
                ConfigurableDeliveryHandler::loadExtraJs($modx, [
                    'status.combo.js',
                    'resource.combo.js',
                    'settings.combo.js',
                    'property.window.js',
                    'properties.grid.js',
                    'properties.tab.js'
                ]);
                break;
        }
        break;
}
