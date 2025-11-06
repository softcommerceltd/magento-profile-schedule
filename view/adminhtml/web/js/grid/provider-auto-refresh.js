/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/provider',
    'jquery'
], function (Provider, $) {
    'use strict';

    return Provider.extend({
        defaults: {
            autoRefreshInterval: 5000, // 5 seconds
            autoRefreshEnabled: true,
            refreshTimer: null
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            if (this.autoRefreshEnabled) {
                this.startAutoRefresh();
            }

            return this;
        },

        /**
         * Start auto-refresh
         */
        startAutoRefresh: function () {
            var self = this;

            this.refreshTimer = setInterval(function () {
                self.reload({
                    refresh: true
                });
            }, this.autoRefreshInterval);

            console.log('Cron Schedule Auto-refresh started (interval: ' + this.autoRefreshInterval + 'ms)');
        },

        /**
         * Stop auto-refresh
         */
        stopAutoRefresh: function () {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
                this.refreshTimer = null;
                console.log('Cron Schedule Auto-refresh stopped');
            }
        },

        /**
         * Toggle auto-refresh
         */
        toggleAutoRefresh: function () {
            if (this.refreshTimer) {
                this.stopAutoRefresh();
                this.autoRefreshEnabled = false;
            } else {
                this.startAutoRefresh();
                this.autoRefreshEnabled = true;
            }
        },

        /**
         * Set refresh interval
         * @param {Number} interval - Interval in milliseconds
         */
        setRefreshInterval: function (interval) {
            this.autoRefreshInterval = interval;

            if (this.autoRefreshEnabled) {
                this.stopAutoRefresh();
                this.startAutoRefresh();
            }
        },

        /**
         * @inheritdoc
         */
        destroy: function () {
            this.stopAutoRefresh();
            this._super();
        }
    });
});