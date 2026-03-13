/* WP Admin Dark Mode – Settings Page JS */
(function ($) {
  'use strict';

  var defaults = {};

  // Collect default colors from data attributes before init
  $('.adm-color-picker').each(function () {
    var key = $(this).attr('name').replace('adm_colors[', '').replace(']', '');
    defaults[key] = $(this).data('default-color');
  });

  // Init WP Color Picker
  $('.adm-color-picker').wpColorPicker();

  // Reset colors to defaults
  $('#adm-reset-colors').on('click', function () {
    $('.adm-color-picker').each(function () {
      var key      = $(this).attr('name').replace('adm_colors[', '').replace(']', '');
      var defColor = $(this).data('default-color');
      if (defColor) {
        $(this).val(defColor).trigger('change');
        // Update WP color picker iris
        var inst = $(this).closest('.wp-picker-container').find('.wp-color-picker');
        inst.wpColorPicker('color', defColor);
      }
    });
  });

}(jQuery));
