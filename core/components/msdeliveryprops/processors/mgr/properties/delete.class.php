<?php
/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

include_once 'base.class.php';

/**
 * Class DeliveryPropertiesDeleteProcessor
 */
class DeliveryPropertiesDeleteProcessor extends DeliveryPropertiesBaseProcessor
{
    public function process()
    {
        $properties = $this->getDeliveryProperties();

        $key = $this->getProperty(self::PROPERTY_KEY);

        if (!array_key_exists($key, $properties) && $key !== 'all') {
            $this->failure('mspp_property_key_not_found');
        }

        if ($key === 'all') {
            $properties = [];
        } else {
            unset($properties[$key]);
        }

        return $this->saveDeliveryProperties($properties)
            ? $this->success()
            : $this->failure($this->modx->lexicon('mspp_save_properties_error'));
    }
}

return DeliveryPropertiesDeleteProcessor::class;
