/* ==========================================================================
   Widget de Accesibilidad — WCAG 2.1 (AA)
   Lógica del botón flotante y del panel de ajustes.
   Persiste la configuración en localStorage y restaura en cada carga.
   ========================================================================== */
(function () {
  'use strict';

  var STORAGE_KEY = 'cenditech_accessibility';
  var root = document.documentElement;

  // Cursor grande (SVG con flecha de 48px, negro con borde blanco)
  var BIG_CURSOR_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">' +
    '<path d="M4 4v36l10-10 6 14 8-6-6-10 16-6z" fill="#000" stroke="#fff" stroke-width="3"/>' +
    '</svg>';

  // Tamaños de fuente disponibles (índice 3 = 18px por defecto)
  var FONT_SIZES = [12, 14, 16, 18, 20, 22, 24, 26];
  var FONT_DEFAULT_INDEX = 3;
  var FONT_MIN_INDEX = 0;
  var FONT_MAX_INDEX = FONT_SIZES.length - 1;

  var defaultSettings = {
    fontIndex: FONT_DEFAULT_INDEX,
    highContrast: false,
    grayscale: false,
    highlightLinks: false,
    readableFont: false,
    textSpacing: false,
    bigCursor: false
  };

  var settings = loadSettings();
  var bigCursorStyleEl = null;

  /* --- Persistencia --- */
  function loadSettings() {
    var parsed = {};
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (raw) {
        parsed = JSON.parse(raw) || {};
      }
    } catch (e) { /* almacenamiento no disponible */ }
    return Object.assign({}, defaultSettings, parsed);
  }

  function saveSettings() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
    } catch (e) { /* almacenamiento no disponible */ }
  }

  /* --- Aplicación de estilos --- */
  function applySettings() {
    root.classList.toggle('acc-high-contrast', settings.highContrast);
    root.classList.toggle('acc-grayscale', settings.grayscale);
    root.classList.toggle('acc-highlight-links', settings.highlightLinks);
    root.classList.toggle('acc-readable-font', settings.readableFont);
    root.classList.toggle('acc-text-spacing', settings.textSpacing);
    root.classList.toggle('acc-big-cursor', settings.bigCursor);

    root.style.fontSize = FONT_SIZES[settings.fontIndex] + 'px';

    updateBigCursorStyle();
    syncControls();
  }

  function updateBigCursorStyle() {
    if (settings.bigCursor) {
      if (!bigCursorStyleEl) {
        bigCursorStyleEl = document.createElement('style');
        bigCursorStyleEl.id = 'acc-big-cursor-style';
        bigCursorStyleEl.textContent =
          'html.acc-big-cursor body, html.acc-big-cursor body * {' +
          ' cursor: url("data:image/svg+xml,' + encodeURIComponent(BIG_CURSOR_SVG) + '") 4 4, auto !important;' +
          '}';
        document.head.appendChild(bigCursorStyleEl);
      }
    } else if (bigCursorStyleEl) {
      bigCursorStyleEl.remove();
      bigCursorStyleEl = null;
    }
  }

  function syncControls() {
    document.querySelectorAll('[data-acc-toggle]').forEach(function (btn) {
      var key = btn.getAttribute('data-acc-toggle');
      btn.setAttribute('aria-pressed', settings[key] ? 'true' : 'false');
    });
  }

  /* --- Acciones --- */
  function toggleFeature(key) {
    settings[key] = !settings[key];
    saveSettings();
    applySettings();
  }

  function changeFont(delta) {
    if (delta === 0) {
      settings.fontIndex = FONT_DEFAULT_INDEX;
    } else {
      settings.fontIndex = Math.min(FONT_MAX_INDEX, Math.max(FONT_MIN_INDEX, settings.fontIndex + delta));
    }
    saveSettings();
    applySettings();
  }

  function resetAll() {
    settings = Object.assign({}, defaultSettings);
    saveSettings();
    applySettings();
  }

  /* --- Apertura / cierre del panel --- */
  var trigger = document.getElementById('acc-trigger');
  var panel = document.getElementById('acc-panel');
  var closeBtn = document.getElementById('acc-close');
  var focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
  var lastFocused = null;

  function openPanel() {
    panel.hidden = false;
    trigger.setAttribute('aria-expanded', 'true');
    lastFocused = document.activeElement;
    // Enfocar el primer control del panel
    var first = panel.querySelector(focusableSelector);
    if (first) {
      first.focus();
    }
  }

  function closePanel() {
    panel.hidden = true;
    trigger.setAttribute('aria-expanded', 'false');
    if (lastFocused) {
      lastFocused.focus();
    }
  }

  function isOpen() {
    return !panel.hidden;
  }

  /* --- Eventos --- */
  if (trigger && panel) {
    trigger.addEventListener('click', function () {
      if (isOpen()) {
        closePanel();
      } else {
        openPanel();
      }
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', closePanel);
    }

    // Cerrar con Escape y devolver el foco al botón
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && isOpen()) {
        closePanel();
      }
    });

    // Trampa de foco simple dentro del panel (Tab / Shift+Tab)
    panel.addEventListener('keydown', function (event) {
      if (event.key !== 'Tab') {
        return;
      }
      var focusable = Array.prototype.filter.call(
        panel.querySelectorAll(focusableSelector),
        function (el) { return el.offsetParent !== null || el === document.activeElement; }
      );
      if (focusable.length === 0) {
        return;
      }
      var first = focusable[0];
      var last = focusable[focusable.length - 1];

      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });

    // Controles de tipo toggle
    panel.querySelectorAll('[data-acc-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        toggleFeature(btn.getAttribute('data-acc-toggle'));
      });
    });

    // Controles de tamaño de texto
    panel.querySelectorAll('[data-acc-font]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var value = btn.getAttribute('data-acc-font');
        changeFont(value === 'reset' ? 0 : parseInt(value, 10));
      });
    });

    // Restablecer todo
    panel.querySelectorAll('[data-acc-reset]').forEach(function (btn) {
      btn.addEventListener('click', resetAll);
    });
  }

  /* --- Mover el widget fuera de <body> ---
     Permite aplicar `filter: grayscale()` a <body> sin que el
     `position: fixed` del botón/panel se reposicione como hijo del body. */
  function detachWidget() {
    var widget = document.getElementById('acc-widget');
    if (widget && widget.parentNode === document.body) {
      document.documentElement.appendChild(widget);
    }
  }

  /* --- Inicialización --- */
  function init() {
    detachWidget();
    applySettings();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
