/**
 * WP Admin Dark Mode – Auto Darken
 *
 * Scans all elements after page load and dynamically darkens any bright
 * backgrounds or dark text colours that are not covered by the main stylesheet.
 * Only active when the "Auto Dark Mode" option is enabled in settings.
 *
 * A MutationObserver watches for dynamically injected nodes (AJAX tables etc.)
 * and applies the same logic to new elements.
 */
(function () {
  'use strict';

  /* ── Luminance helpers ──────────────────────────────────────────────────── */

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
   * @returns {string} rgb() string
   */
  function darkenRgb(rgb, factor) {
    return 'rgb(' +
      Math.round(rgb[0] * factor) + ',' +
      Math.round(rgb[1] * factor) + ',' +
      Math.round(rgb[2] * factor) + ')';
  }

  /**
   * Lighten an rgb array towards white by a factor.
   * @param {number[]} rgb
   * @param {number}   factor  (> 1 = lighter, e.g. 2.5)
   * @returns {string} rgb() string
   */
  function lightenRgb(rgb, factor) {
    return 'rgb(' +
      Math.min(255, Math.round(rgb[0] * factor)) + ',' +
      Math.min(255, Math.round(rgb[1] * factor)) + ',' +
      Math.min(255, Math.round(rgb[2] * factor)) + ')';
  }

  /* ── Element processing ─────────────────────────────────────────────────── */

  // Tags we never touch (images, videos, svg paths, scripts, etc.)
  var SKIP_TAGS = new Set([
    'IMG', 'VIDEO', 'CANVAS', 'IFRAME', 'SCRIPT', 'STYLE', 'SVG', 'PATH',
    'CIRCLE', 'RECT', 'LINE', 'POLYLINE', 'POLYGON', 'DEFS', 'USE', 'SYMBOL',
  ]);

  // Marker attribute so we never process the same element twice
  var PROCESSED_ATTR = 'data-adm-darkened';

  /**
   * Apply auto-dark logic to a single element.
   * @param {Element} el
   */
  function processElement(el) {
    if (!(el instanceof HTMLElement))   return;
    if (SKIP_TAGS.has(el.tagName))      return;
    if (el.hasAttribute(PROCESSED_ATTR)) return;

    el.setAttribute(PROCESSED_ATTR, '1');

    var style = window.getComputedStyle(el);

    // ── Background ────────────────────────────────────────────────────────
    var bg = parseRgb(style.backgroundColor);
    if (bg) {
      var bgLum = luminance(bg);
      // Fully transparent backgrounds have alpha 0 – getComputedStyle returns
      // rgba(0,0,0,0) which parses as [0,0,0] with lum 0 → we skip those.
      var isTransparent = style.backgroundColor.indexOf('rgba') === 0 &&
                          style.backgroundColor.indexOf(', 0)') !== -1;
      if (!isTransparent && bgLum > 0.15) {
        // Bright background → push it into the dark range
        el.style.backgroundColor = darkenRgb(bg, 0.15);
      }
    }

    // ── Text colour ───────────────────────────────────────────────────────
    var fg = parseRgb(style.color);
    if (fg) {
      var fgLum = luminance(fg);
      if (fgLum < 0.20) {
        // Very dark text (near-black) → lighten to be readable
        el.style.color = lightenRgb(fg, 8);
      }
    }

    // ── Border ────────────────────────────────────────────────────────────
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

    // Also process the root itself if it's an element
    if (root instanceof HTMLElement) processElement(root);

    for (var i = 0; i < els.length; i++) {
      processElement(els[i]);
    }
  }

  /* ── Boot ───────────────────────────────────────────────────────────────── */

  function init() {
    // Initial pass over the full page
    processTree(document);

    // Watch for dynamically added nodes (AJAX, React, Vue etc.)
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType !== 1) return; // elements only
          processTree(/** @type {Element} */ (node));
        });
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree:   true,
    });
  }

  // Run after DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

}());
