/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'mageUtils',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/lib/collapsible',
], function (_, registry, utils, Component, modal, confirm, $t, $, Collapsible) {
    'use strict';

    
     return Component.extend({
        
        defaults: {
            selectProvider: 'ns = ${ $.ns }, index = ids',
            modules: {
                selections: '${ $.selectProvider }'
            }
        },
        
         /**
         * Retrieves selections data from the selections provider.
         *
         * @returns {Object|Undefined}
         */
        getSelections: function () {
            var provider = this.selections(),
                selections = provider && provider.getSelections();

            return selections;
        },
        
        switchOwner: function () {
            var data = this.getSelections();
            var $_orderids = data.selected;
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
                title: 'Switch Owner',
                modalClass: 'switchownerpopup',
                buttons: [{
                    text: $.mage.__('Continue'),
                    class: '',
                    click: function () {
                        $('#switch-owner-form').submit();
                    }
                }]
            };
            
            var popup = modal(options, $('#switchowner-maindiv'));               
            $('#switchowner-maindiv').modal('openModal');
            $('#switchowner-maindiv').css('display', 'block');
            $('#order_ids').val($_orderids);

          },
          

    });
  
});