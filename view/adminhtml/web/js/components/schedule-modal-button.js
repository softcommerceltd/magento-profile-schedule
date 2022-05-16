/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'SoftCommerce_Core/js/components/button-extended',
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'underscore'
], function (Button, async, uiRegistry, _) {
    'use strict';

    return Button.extend({
        defaults: {
            listens: {
                scheduleId: 'toggleComponentButtons'
            },
        },

        /**
         * Initializes component.
         * @returns {Button} Chainable.
         */
        initialize: function () {
            this._super();

            if (!_.isUndefined(this.editMode) && false !== this.editMode) {
                return this;
            }

            let self = this;
            async.async('[data-index="schedule_id"]', document.getElementById('container'), function (node) {
                let elementName = self.parentName + '.schedule_id';
                if (uiRegistry.has(elementName)) {
                    let option = uiRegistry.get(elementName).options()[0];
                    if (_.isObject(option)) {
                        self.disabled(true);
                    }
                }
            });

            return this;
        },


        /**
         * @inheritDoc
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            if (action.params && action.params[0]) {
                action.params[0]['id'] = this.scheduleId;
            } else {
                action.params = [{
                    'id': this.scheduleId
                }];
            }

            this._super();
        },

        /**
         * Toggles related component management buttons.
         */
        toggleComponentButtons: function () {
            if (_.isUndefined(this.editMode) || _.isNull(this.scheduleId)) {
                return;
            }

            if (!_.isNumber(this.scheduleId)) {
                this.disabled(this.scheduleId.length ? !this.editMode : this.editMode);
            } else {
                this.disabled(this.scheduleId.length ? this.editMode : !this.editMode);
            }
        },
    });
});
