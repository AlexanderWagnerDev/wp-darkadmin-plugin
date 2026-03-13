/**
 * WP Admin Dark Mode - Auto Darken
 *
 * Scans all elements after page load and dynamically darkens any bright
 * backgrounds or dark text colours not covered by the main stylesheet.
 * Only active when the "Auto Dark Mode" option is enabled in settings.
 *
 * A MutationObserver watches for dynamically injected nodes (AJAX, React etc.)
 * and applies the same logic to new elements.
 */
(function () {
  'use strict';

  /**
   * Parse an rgb/rgba string into [r, g, b] (0-255) or null.
   * @param {string} color
   * @returns {number[]|null}
   */
  function parseRgb(color) {
    var m = color.match(/rgba?\(\s*(\d+),\s*(\d+),\s*(\d+)/);
    return m ? [parseInt(m[1]), parseInt(m[2]), parseInt(m[3])] : null;
  }

  /**
   * Relative luminance (0 = black, 1 = white) per WCAG 2.1.
   * @param {number[]} rgb
   * @returns {number}
   */
  function luminance(rgb) {
    var c = rgb.map(function (v) {
      v /= 255;
      return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
  }

  /**
   * Darken an rgb array by a factor (0 = black, 1 = unchanged).
   * @param {number[]} rgb
   * @param {number}   factor
   * @returns {string}
   */
  function darkenRgb(rgb, factor) {
    return 'rgb(' +
      Math.round(rgb[0] * factor) + ',' +
      Math.round(rgb[1] * factor) + ',' +
      Math.round(rgb[2] * factor) + ')';
  }

  /**
   * Lighten an rgb array towards white by a multiplier.
   * @param {number[]} rgb
   * @param {number}   factor
   * @returns {string}
   */
  function lightenRgb(rgb, factor) {
    return 'rgb(' +
      Math.min(255, Math.round(rgb[0] * factor)) + ',' +
      Math.min(255, Math.round(rgb[1] * factor)) + ',' +
      Math.min(255, Math.round(rgb[2] * factor)) + ')';
  }

  var SKIP_TAGS = new Set([
    'IMG', 'VIDEO', 'CANVAS', 'IFRAME', 'SCRIPT', 'STYLE', 'SVG', 'PATH',
    'CIRCLE', 'RECT', 'LINE', 'POLYLINE', 'POLYGON', 'DEFS', 'USE', 'SYMBOL',
  ]);

  var PROCESSED_ATTR = 'data-adm-darkened';

  /**
   * Apply auto-dark logic to a single element.
   * @param {Element} el
   */
  function processElement(el) {
    if (!(el instanceof HTMLElement))    return;
    if (SKIP_TAGS.has(el.tagName))       return;
    if (el.hasAttribute(PROCESSED_ATTR)) return;

    el.setAttribute(PROCESSED_ATTR, '1');

    var style = window.getComputedStyle(el);

    var bg = parseRgb(style.backgroundColor);
    if (bg) {
      var bgLum = luminance(bg);
      var isTransparent = style.backgroundColor.indexOf('rgba') === 0 &&
                          style.backgroundColor.indexOf(', 0)') !== -1;
      if (!isTransparent && bgLum > 0.15) {
        el.style.backgroundColor = darkenRgb(bg, 0.15);
      }
    }

    var fg = parseRgb(style.color);
    if (fg && luminance(fg) < 0.20) {
      el.style.color = lightenRgb(fg, 8);
    }

    var bc = parseRgb(style.borderColor);
    if (bc && luminance(bc) > 0.40) {
      el.style.borderColor = darkenRgb(bc, 0.25);
    }
  }

  /**
   * Walk every element inside a root node.
   * @param {Element|Document} root
   */
  function processTree(root) {
    var els = (root === document)
      ? document.querySelectorAll('*')
      : root.querySelectorAll ? root.querySelectorAll('*') : [];

    if (root instanceof HTMLElement) processElement(root);

    for (var i = 0; i < els.length; i++) {
      processElement(els[i]);
    }
  }

  function init() {
    processTree(document);

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType !== 1) return;
          processTree(/** @type {Element} */ (node));
        });
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree:   true,
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

}());
