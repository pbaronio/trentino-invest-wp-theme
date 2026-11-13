/**
 * Trentino Invest — sicurezza dello scroll.
 *
 * Durante l'animazione di apertura (loader.js) lo scroll è bloccato:
 *   - classe html.is--scroll-locked (overflow: hidden, in loader.css)
 *   - window.lenis.stop()
 * e viene sbloccato a fine animazione. Questo file garantisce che lo sblocco
 * avvenga SEMPRE, qualunque cosa succeda:
 *   1. a fine animazione (evento "loaderComplete");
 *   2. comunque dopo un tempo massimo, se l'animazione non arriva in fondo
 *      (scheda in background, errore JS, script bloccato da un'estensione...);
 *   3. se Lenis va in errore, lo spegne e torna allo scroll nativo del browser.
 */
(function () {
  'use strict';

  // Durata massima dell'animazione di apertura (in loader.js dura ~6 s).
  var MAX_LOCK_MS = 9000;
  var html = document.documentElement;
  var released = false;

  /* ---------- Lenis: controllo di salute e ripiego sullo scroll nativo ---------- */

  function lenisIsBroken() {
    var l = window.lenis;
    if (!l) return false;
    try {
      var limit = l.limit; // getter che legge le dimensioni: se non funziona Lenis non può scrollare
      return typeof limit !== 'number' || isNaN(limit);
    } catch (e) {
      return true;
    }
  }

  function fallbackToNativeScroll(reason) {
    var l = window.lenis;
    if (!l || l.__tiNative) return;
    try { l.destroy(); } catch (e) { /* già rotto */ }
    html.classList.remove('lenis', 'lenis-smooth', 'lenis-stopped', 'lenis-scrolling');
    // Oggetto compatibile con le chiamate degli altri script (start/stop/scrollTo/on).
    window.lenis = {
      __tiNative: true,
      isStopped: false,
      start: function () { this.isStopped = false; },
      stop: function () { this.isStopped = true; },
      scrollTo: function (target, opts) {
        var y = typeof target === 'number' ? target : 0;
        if (target && target.getBoundingClientRect) y = target.getBoundingClientRect().top + window.scrollY;
        window.scrollTo({ top: y, behavior: opts && opts.immediate ? 'auto' : 'smooth' });
      },
      on: function () {},
      off: function () {},
      raf: function () {},
      resize: function () {},
      destroy: function () {}
    };
    if (window.console) console.warn('[Trentino Invest] Lenis disattivato, uso lo scroll nativo:', reason);
  }

  // Se Lenis va in errore durante lo scroll, si passa allo scroll nativo.
  window.addEventListener('error', function (ev) {
    var src = (ev && ev.filename) || '';
    if (src.indexOf('lenis') !== -1) fallbackToNativeScroll(ev.message);
  });

  /* ---------- Sblocco garantito ---------- */

  function release() {
    if (lenisIsBroken()) fallbackToNativeScroll('dimensioni non valide');
    if (released) return;
    released = true;
    html.classList.remove('is--scroll-locked');
    var header = document.querySelector('.crisp-header');
    if (header) header.classList.remove('is--hidden');
    if (window.lenis && typeof window.lenis.start === 'function') {
      try { window.lenis.start(); } catch (e) { fallbackToNativeScroll(e.message); }
    }
    if (window.ScrollTrigger) {
      try { window.ScrollTrigger.refresh(); } catch (e) { /* niente */ }
    }
  }

  window.addEventListener('loaderComplete', function () {
    // Il loader sblocca da solo: qui ci assicuriamo solo che Lenis sia sano e ripartito.
    released = false;
    release();
  });

  function armTimeout() {
    // Senza animazione di apertura (pagine interne) non c'è nulla da sbloccare, ma Lenis va controllato.
    if (!document.querySelector('.crisp-header')) {
      if (lenisIsBroken()) fallbackToNativeScroll('dimensioni non valide');
      return;
    }
    setTimeout(function () {
      var header = document.querySelector('.crisp-header');
      var stillLoading = header && header.classList.contains('is--loading');
      if (html.classList.contains('is--scroll-locked') || (window.lenis && window.lenis.isStopped)) {
        if (window.console && stillLoading) console.warn('[Trentino Invest] animazione di apertura non completata: sblocco lo scroll');
        released = false;
        release();
      }
    }, MAX_LOCK_MS);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', armTimeout);
  } else {
    armTimeout();
  }

  // Ritorno alla pagina dalla cache del browser (tasto indietro): mai restare bloccati.
  window.addEventListener('pageshow', function (ev) {
    if (ev.persisted) armTimeout();
  });
})();
