/*
 * Dimension WP Theme — Animation Fix
 * 
 * Problem: WordPress loads scripts in the footer, so by the time main.js runs,
 * the window 'load' event has already fired. The is-preload class never gets 
 * removed, so all entry animations are permanently blocked.
 *
 * Fix: Check if the document is already loaded and remove is-preload immediately,
 * otherwise wait for the load event.
 */
(function($) {
    function removePreload() {
        window.setTimeout(function() {
            $('body').removeClass('is-preload');
        }, 100);
    }

    // If page already loaded (scripts in footer = always true), fire immediately
    if (document.readyState === 'complete') {
        removePreload();
    } else {
        $(window).on('load', removePreload);
    }
})(jQuery);
