<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

set_time_limit(0);
error_reporting(E_ALL | E_STRICT); ini_set('display_errors',true);

ini_set('date.timezone', 'Europe/Minsk');

define('PKG_NAME', 'msDeliveryProps');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '0.0.1');
define('PKG_RELEASE', 'beta');
define('PKG_SUPPORTS_PHP', '7.2');
define('PKG_SUPPORTS_MODX', '2.7');
define('PKG_SUPPORTS_MS2', '2.5');

require_once __DIR__ . '/vendor/modx/revolution/core/xpdo/xpdo.class.php';

/* instantiate xpdo instance */
$xpdo = new xPDO('mysql:host=localhost;dbname=modx;charset=utf8', 'root', '',
    [xPDO::OPT_TABLE_PREFIX => 'modx_', xPDO::OPT_CACHE_PATH => __DIR__ . '/../../../core/cache/'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]
);
$cacheManager= $xpdo->getCacheManager();
$xpdo->setLogLevel(xPDO::LOG_LEVEL_INFO);
$xpdo->setLogTarget();

$root = dirname(__DIR__) . '/';
$sources = [
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'docs' => $root . 'docs/',
    'resolvers' => $root . '_build/resolvers/',
    'validators' => $root . '_build/validators/',
    'implants' => $root . '_build/implants/',
    'plugins' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'assets' => ['components/msdeliveryprops/'],
    'core' => ['components/msdeliveryprops/']
];

$signature = implode('-', [PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE]);

$release = false;
if (!empty($argv) && $argc > 1) {
    $release = $argv[1];
}

$directory = $release === 'release' ? $root . '_packages/' : __DIR__ . '/../../core/packages/';
$filename = $directory . $signature . '.transport.zip';

/* remove the package if it's already been made */
if (file_exists($filename)) {
    unlink($filename);
}
if (file_exists($directory . $signature) && is_dir($directory . $signature)) {
    $cacheManager = $xpdo->getCacheManager();
    if ($cacheManager) {
        $cacheManager->deleteTree($directory . $signature, true, false, []);
    }
}

$xpdo->loadClass('transport.xPDOTransport', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOFileVehicle', XPDO_CORE_PATH, true, true);
$xpdo->loadClass('transport.xPDOScriptVehicle', XPDO_CORE_PATH, true, true);

$package = new xPDOTransport($xpdo, $signature, $directory);

$xpdo->setPackage('modx', __DIR__ . '/vendor/modx/revolution/core/model/');
$xpdo->loadClass(modAccess::class);
$xpdo->loadClass(modAccessibleObject::class);
$xpdo->loadClass(modAccessibleSimpleObject::class);
$xpdo->loadClass(modPrincipal::class);
$xpdo->loadClass(modElement::class);
$xpdo->loadClass(modScript::class);

$namespace = $xpdo->newObject(modNamespace::class);
$namespace->fromArray([
    'id' => PKG_NAME_LOWER,
    'name' => PKG_NAME_LOWER,
    'path' => '{core_path}components/' . PKG_NAME_LOWER . '/',
]);

$package->put($namespace, [
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::NATIVE_KEY => PKG_NAME_LOWER,
    'namespace' => PKG_NAME_LOWER
]);

$category = $xpdo->newObject(modCategory::class);
$category->fromArray(['id' => 1, 'category' => PKG_NAME, 'parent' => 0]);

$plugins = include $sources['data'] . 'transport.plugins.php';
if (is_array($plugins)) {
    $category->addMany($plugins, 'Plugins');
}

$validators = [];
array_push($validators,
    ['type' => 'php', 'source' => $sources['validators'] . 'validate.phpversion.php'],
    ['type' => 'php', 'source' => $sources['validators'] . 'validate.modxversion.php']
);

$resolvers = [];
foreach ($sources['assets'] as $file) {
    $directory = dirname($file);
    $resolvers[] = [
        'type' => 'file',
        'source' => $root . 'assets/' . $file,
        'target' => "return MODX_ASSETS_PATH . '$directory/';",
    ];
}
foreach ($sources['core'] as $file) {
    $directory = dirname($file);
    $resolvers[] = [
        'type' => 'file',
        'source' => $root . 'core/' . $file,
        'target' => "return MODX_CORE_PATH . '$directory/';"
    ];
}

$package->put($category, [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
        'Plugins' => [
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNINSTALL_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
                'PluginEvents' => [
                    xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNINSTALL_OBJECT => true,
                    xPDOTransport::RELATED_OBJECTS => false
                ]
            ]
        ]
    ],
    xPDOTransport::NATIVE_KEY => true,
    'package' => 'modx',
    'resolve' => $resolvers,
    'validate' => $validators
]);

$package->setAttribute('changelog', file_get_contents($sources['docs'] . 'changelog.txt'));
$package->setAttribute('license', file_get_contents($sources['docs'] . 'license.txt'));
$package->setAttribute('readme', file_get_contents($sources['docs'] . 'readme.txt'));
$package->setAttribute('requires', [
    'php' => '>=' . PKG_SUPPORTS_PHP,
    'modx' => '>=' . PKG_SUPPORTS_MODX,
    'miniShop2' => '>=' . PKG_SUPPORTS_MS2
]);

if ($package->pack()) {
    $xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package built');
}
