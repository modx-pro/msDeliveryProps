/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

msDeliveryProps.combo.Settings = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        name: 'key',
        hiddenName: 'key',
        displayField: 'name_trans',
        valueField: 'key',
        fields: ['key', 'value', 'name_trans', 'xtype'],
        pageSize: 20,
        typeAhead: false,
        preselectValue: false,
        allowBlank: true,
        emptyText: _('mspp_select_property'),
        editable: false,
        hideMode: 'offsets'
    });

    msDeliveryProps.combo.Settings.superclass.constructor.call(this, config);
};

Ext.extend(msDeliveryProps.combo.Settings, MODx.combo.ComboBox);
Ext.reg('mspp-combo-settings', msDeliveryProps.combo.Settings);
