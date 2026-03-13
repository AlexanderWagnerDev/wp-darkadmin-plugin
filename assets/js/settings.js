/* WP Admin Dark Mode - Settings Page JS */
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

  // Reset all color pickers to their default values
  $('#adm-reset-colors').on('click', function () {
    $('.adm-color-picker').each(function () {
      var defColor = $(this).data('default-color');
      if (defColor) {
        $(this).val(defColor).trigger('change');
        var inst = $(this).closest('.wp-picker-container').find('.wp-color-picker');
        inst.wpColorPicker('color', defColor);
      }
    });
  });

}(jQuery));
