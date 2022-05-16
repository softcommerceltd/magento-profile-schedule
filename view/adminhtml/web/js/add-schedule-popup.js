/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'jquery/ui',
    'mage/translate'
], function ($, _) {
    'use strict';

    $.widget('mage.profileSchedulePopup', {
        /** @inheritDoc */
        _create: function () {
            this._on({
                'click': '_showPopup'
            });
        },

        /**
         * @private
         */
        _initModal: function () {
            let self = this;

            this.modal = $('<div id="create_new_schedule"/>').modal({
                title: $.mage.__('Create New Schedule'),
                type: 'slide',
                buttons: [],

                /**
                 * @inheritDoc
                 */
                opened: function () {
                    $(this).parent().addClass('modal-content-new-schedule');
                    self.iframe = $('<iframe id="create_new_schedule_container">').attr({
                        src: self.options.url,
                        frameborder: 0
                    });

                    self.modal.append(self.iframe);
                    self._changeIframeSize();
                    $(window).off().on('resize.modal', _.debounce(self._changeIframeSize.bind(self), 400));
                },

                /**
                 * @inheritDoc
                 */
                closed: function () {
                    let doc = self.iframe.get(0).document;

                    if (doc && $.isFunction(doc.execCommand)) {
                        doc.execCommand('stop');
                        self.iframe.remove();
                    }
                    self.modal.data('modal').modal.remove();
                    $(window).off('resize.modal');
                }
            });
        },

        /**
         * @return {Number}
         * @private
         */
        _getHeight: function () {
            let modal = this.modal.data('modal').modal,
                modalHead = modal.find('header'),
                modalHeadHeight = modalHead.outerHeight(),
                modalHeight = modal.outerHeight(),
                modalContentPadding = this.modal.parent().outerHeight() - this.modal.parent().height();

            return modalHeight - modalHeadHeight - modalContentPadding;
        },

        /**
         * @return {Number}
         * @private
         */
        _getWidth: function () {
            return this.modal.width();
        },

        /**
         * @private
         */
        _changeIframeSize: function () {
            this.modal.parent().outerHeight(this._getHeight());
            this.iframe.outerHeight(this._getHeight());
            this.iframe.outerWidth(this._getWidth());

        },

        /**
         * @private
         */
        _showPopup: function () {
            this._initModal();
            this.modal.modal('openModal');
        }
    });

    return $.mage.profileSchedulePopup;
});
