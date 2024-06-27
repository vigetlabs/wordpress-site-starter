/**
 * This allows the user to skip the cropping step when uploading a site icon.
 *
 * @package VigetWP
 */

(function($, wp) {
    $(document).ready(function() {
        if (!wp.media) {
            return;
        }

        const originalController = wp.media.controller.SiteIconCropper;

        wp.media.controller.SiteIconCropper = originalController.extend({
            activate: function() {
                originalController.prototype.activate.apply(this, arguments);
                this.set('canSkipCrop', true);
            }
        });

        const originalButton = wp.media.view.Button;

        wp.media.view.Button = originalButton.extend({
            click: function( e ) {
                if (!this.options.classes.includes('media-button-skip')) {
                    originalButton.prototype.click.apply(this, arguments);
                    return;
                }

                e.preventDefault();

                const controller = this.controller;
                const attachment = controller.state().get('selection').first().toJSON();
                controller.trigger('cropped', attachment);
                controller.close();
            }
        });
    });
})(jQuery, wp);
