/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/grid/tree-massactions'
], function (Massactions) {
    'use strict';

    return Massactions.extend({

        /**
         * Adds search parameter to URL request data.
         * @returns {Object|Undefined}
         */
        getSelections: function () {
            let selections = this._super();

            if (this.source.params['job_code']) {
                selections.params.job_code = this.source.params['job_code'];
            }

            return selections;
        }
    });
});
