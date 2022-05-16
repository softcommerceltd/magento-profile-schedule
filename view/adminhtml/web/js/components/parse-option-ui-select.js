/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'SoftCommerce_Core/js/components/parse-option-ui-select',
    'underscore',
], function ($, alert, Select, _) {
    'use strict';

    return Select.extend({
        /**
         * @param {Object} response - Response data object.
         * @returns {Object}
         */
        setParsed: function (response) {
            if (response.error || !response.params.type_id) {
                return this;
            }

            let typeId = this.source.get('data.profile_entity.type_id'),
                responseTypeId = response.params.type_id;

            if (typeId !== responseTypeId) {
                alert({
                    title: $.mage.__('Schedule Misconfiguration.'),
                    content: this.buildAlertMessage(response.params, typeId),
                    modalClass: 'alert',
                    clickableOverlay: true
                });

                return;
            }

            return this._super();
        },

        /**
         * @param {Object} response
         * @param {string} typeId
         * @returns {string}
         */
        buildAlertMessage: function (response, typeId) {
            let html = '<div class="messages">',
                responseLabel = response.label,
                content = $.mage.__(
                    '<p>"%s" scheduler can not be used with <b>%p</b> profile.</p>'
                    + '<p>Please choose or create scheduler type <b>%p</b>.</p>'
                );

            content = content.replace('%s', responseLabel).replace('%p', typeId).replace('%p', typeId);
            html += '<div class="message message-notice">';
            html += '<div class="messages-message-notice">' + content + '</div>';
            html += '</div>';
            return html + '</div>';
        },
    });
});

