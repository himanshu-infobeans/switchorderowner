/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'Magento_Ui/js/modal/modal'
], function ($, _, template, modal) {
    'use strict';

    function main(config, element) {
        var $_fetchCustomerUrl = config.fetchCustomerUrl;
        $(document).on('change', '#store_group_id', function () {
            var param = 'store=1';
            $.ajax({
                showLoader: true,
                url: $_fetchCustomerUrl,
                data: param,
                type: "GET",
                dataType: 'json'
            }).done(function (data) {
                $('#switchowner-customer-dropdown').children('div').replaceWith(data.customerOptions);
                $('.chosen-select').chosen();
            });
        });

        $(document).on('change', '#customer-list', function () {
            var customer_id = $('#customer-list').val();
            $('#customer_id').val(customer_id);
        });

        $(document).on('click', '#switchowner', function () {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
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
        });
            
        
    };
    return main;
});