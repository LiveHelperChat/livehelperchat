<?php
// Change to your Script identifier
if ($ext == 'dummy_Script identifier') : ?>
(function () {
    window.lhcHelperfunctions.eventEmitter.addListener('dummy-script-identifier', function (params, dispatch, getstate) {
        setTimeout( function() {
<?php
/**
 *
 * // Generate your script using PHP
 *
 *
if (isset($chat)) {
 *          // We have a chat
 *      }
 *
 *      // Array of departments
 *      if (isset($dep)) {
// We have department
 *      }
 *
 *      // Inform that extension has handled
 *      $extHandled = true;
 *
 *
 *
 */
?>
        }, (typeof params['delay'] != 'undefined' ? parseInt(params['delay']) * 1000 : 0));
    });
})();
<?php $extHandled = true; endif; ?>