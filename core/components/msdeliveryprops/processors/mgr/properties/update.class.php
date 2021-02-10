<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

include_once 'base.class.php';

/**
 * Class msDeliveryPropsDeliveryPropertiesUpdateProcessor
 */
class DeliveryPropertiesUpdateProcessor extends DeliveryPropertiesBaseProcessor
{
    public function process()
    {
        $properties = $this->getDeliveryProperties();

        $key = $this->getProperty(self::PROPERTY_KEY);
        $value = $this->getProperty(self::PROPERTY_VALUE);

        $properties[$key] = $value; // overwrite

        return $this->saveDeliveryProperties($properties)
            ? $this->success()
            : $this->failure($this->modx->lexicon('mspp_save_properties_error'));
    }
}

return DeliveryPropertiesUpdateProcessor::class;
