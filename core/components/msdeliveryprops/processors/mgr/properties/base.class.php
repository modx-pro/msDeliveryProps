<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

/**
 * Class DeliveryPropertiesBaseProcessor
 */
class DeliveryPropertiesBaseProcessor extends modProcessor
{
    protected const PROPERTY_PAYMENT = 'delivery';
    protected const PROPERTY_KEY = 'key';
    protected const PROPERTY_VALUE = 'value';

    /** @var msDelivery */
    protected $delivery;

    /**
     * @return msDelivery|object
     */
    protected function getDelivery()
    {
        if (!$this->delivery) {
            $this->delivery = $this->modx->getObject('msDelivery', $this->getProperty(self::PROPERTY_PAYMENT));
        }

        return $this->delivery;
    }

    /**
     * @return mixed
     */
    protected function getDeliveryProperties()
    {
        return $this->getDelivery()->get('properties');
    }

    /**
     * @param array $properties
     * @return bool
     */
    protected function saveDeliveryProperties(array $properties = [])
    {
        $this->delivery->set('properties', $properties);

        return $this->delivery->save();
    }

    public function process() {}
}
