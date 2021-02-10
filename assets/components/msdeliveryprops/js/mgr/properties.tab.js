/**
 * The MIT License
 * Copyright (c) 2019 Ivan Klimchuk. https://klimchuk.com
 * Full license text placed in the LICENSE file in the repository or in the license.txt file in the package.
 */

Ext.ComponentMgr.onAvailable('minishop2-window-delivery-update', function () {
    this.on('beforerender', function (deliveryWindow) {
        const tabs = this.findByType('modx-tabs').pop();

        if (!deliveryWindow.record.class) {
            return;
        }

        tabs.add({
            title: _('properties'),
            items: [{
                xtype: 'mspp-grid-delivery-properties',
                delivery: deliveryWindow.record.id,
                suffix: deliveryWindow.record.class.toLowerCase()
            }]
        });
    });
});
