/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

var msDeliveryProps = function(config) {
    config = config || {};
    msDeliveryProps.superclass.constructor.call(this, config);
};
Ext.extend(msDeliveryProps, Ext.Component, { combo: {}, grid: {}, window: {} });
Ext.reg('mspp', msDeliveryProps);

msDeliveryProps = new msDeliveryProps();
