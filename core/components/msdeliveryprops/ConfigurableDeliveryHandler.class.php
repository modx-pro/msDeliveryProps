<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

// need to load
if (!class_exists('msDeliveryHandler')) {
    $path = MODX_CORE_PATH . '/components/minishop2/model/minishop2/msdeliveryhandler.class.php';
    if (is_readable($path)) {
        require_once $path;
    }
}

/**
 * Class BePaid
 */
abstract class ConfigurableDeliveryHandler extends msDeliveryHandler
{
    /** @var modX */
    public $modx;

    /** @var miniShop2 */
    public $ms2;

    /** @var array */
    public $config = [];

    /**
     * @param xPDOObject $object
     * @param array $config
     */
    public function __construct(xPDOObject $object, array $config = [])
    {
        parent::__construct($object, $config);

        $this->config = $config;
    }

    /**
     * @param modX $modx
     * @param array $files
     */
    public static function loadExtraJs(modX $modx, array $files = []): void
    {
        $ms2connector = $modx->getOption('minishop2.assets_url', null, $modx->getOption('assets_url') . 'components/minishop2/') . 'connector.php';
        $ownConnector = $modx->getOption('assets_url') . 'components/msdeliveryprops/connector.php';

        $modx->controller->addLexiconTopic('minishop2:default');
        $modx->controller->addLexiconTopic('msdeliveryprops:default');
        $modx->controller->addLexiconTopic('core:propertyset');
        $modx->controller->addJavascript(MODX_ASSETS_URL . 'components/msdeliveryprops/js/mgr/msdeliveryprops.js');
        $modx->controller->addHtml('<script>msDeliveryProps.ms2Connector = "' . $ms2connector . '";</script>');
        $modx->controller->addHtml('<script>msDeliveryProps.ownConnector = "' . $ownConnector . '";</script>');

        foreach ($files as $file) {
            $modx->controller->addLastJavascript(MODX_ASSETS_URL . 'components/msdeliveryprops/js/mgr/' . $file);
        }
    }

    abstract public static function getPrefix(): string;

    /**
     * @param msDelivery $payment
     * @return array
     * @throws ReflectionException
     */
    public function getProperties(msDelivery $payment): array
    {
        if (!is_subclass_of(get_class($payment), msDelivery::class)) {
            $this->log('Passed object is not a payment object');
            return $this->config;
        }

        $properties = $payment->get('properties') ?: [];

        $reflection = new ReflectionClass(static::class);

        $configuration = [];
        foreach ($reflection->getConstants() as $constant => $value) {
            if (strpos($constant, 'OPTION') !== 0) { continue; }
            $key = static::getPrefix() . '_' . $value;
            $configuration[$value] = array_key_exists($key, $properties)
                ? $this->processYesNo($properties[$key])
                : $this->getMODX()->getOption($key, null);
        }

        return array_merge($configuration, $this->config);
    }

    private function processYesNo($value)
    {
        $managerLanguage = $this->modx->getOption('manager_language');
        $this->modx->lexicon->load($managerLanguage . ':core:default');

        $map = [
            $this->modx->lexicon('yes', [], $managerLanguage) => true,
            $this->modx->lexicon('no', [], $managerLanguage) => false
        ];

        return $map[$value] ?? $value;
    }

    protected function getMODX(): xPDO
    {
        return $this->modx;
    }

    protected function log(string $msg): void
    {
        $this->getMODX()->log(modX::LOG_LEVEL_ERROR, sprintf('[ms2::payment::%s] %s', strtolower(get_class($this)), $msg));
    }
}
