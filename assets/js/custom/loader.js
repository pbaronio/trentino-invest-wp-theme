// Loader disattivato temporaneamente.
// Per riattivarlo: commenta il blocco "LOADER DISABLED" qui sotto
// e rimuovi il commento dal blocco "LOADER ENABLED" in fondo al file.

/*
// --- LOADER DISABLED (stub) ---
function whenLoaderReady(callback) {
  callback();
}

window.whenLoaderReady = whenLoaderReady;

function disableLoader() {
  const container = document.querySelector(".crisp-header");
  if (container) {
    container.classList.remove("is--loading", "is--hidden");
  }

  document.documentElement.classList.remove("is--scroll-locked");

  if (window.lenis) {
    window.lenis.start();
  }

  window.dispatchEvent(new Event("loaderComplete"));
}

document.addEventListener("DOMContentLoaded", disableLoader);
window.addEventListener("pageshow", disableLoader);
// --- END LOADER DISABLED ---

*/

// --- LOADER ENABLED ---
gsap.registerPlugin(SplitText, CustomEase);
CustomEase.create("slideshow-wipe", "0.625, 0.05, 0, 1");

if ("scrollRestoration" in history) {
  history.scrollRestoration = "manual";
}

let loaderTimeline = null;

// Show the loader again after this time (ms). Set to 0 to show only once ever.
const LOADER_TTL_MS = 7 * 24 * 60 * 60 * 1000;
const LOADER_STORAGE_KEY = "ti-loader-seen";

function shouldShowLoader() {
  if (document.documentElement.classList.contains("wf-design-mode") ||
      document.documentElement.classList.contains("wf-editor")) {
    return true;
  }

  try {
    const seenAt = Number(localStorage.getItem(LOADER_STORAGE_KEY));
    if (!seenAt) return true;
    if (!LOADER_TTL_MS) return false;
    return Date.now() - seenAt > LOADER_TTL_MS;
  } catch {
    return true;
  }
}

function markLoaderSeen() {
  try {
    localStorage.setItem(LOADER_STORAGE_KEY, String(Date.now()));
  } catch {
    // localStorage unavailable (private mode, etc.)
  }
}

function skipLoader() {
  const container = document.querySelector(".crisp-header");
  if (container) {
    container.classList.remove("is--loading", "is--hidden");
  }

  setLoaderScrollLock(false);
  if (window.ScrollTrigger) ScrollTrigger.refresh();
  window.dispatchEvent(new Event("loaderComplete"));
}

function resetScrollPosition() {
  window.scrollTo(0, 0);
  if (window.lenis) {
    window.lenis.scrollTo(0, { immediate: true });
  }
}

resetScrollPosition();

function whenLoaderReady(callback) {
  if (!document.querySelector(".crisp-header.is--loading")) {
    callback();
    return;
  }
  window.addEventListener("loaderComplete", callback, { once: true });
}

window.whenLoaderReady = whenLoaderReady;

function setLoaderScrollLock(locked) {
  document.documentElement.classList.toggle("is--scroll-locked", locked);
  if (window.lenis) {
    locked ? window.lenis.stop() : window.lenis.start();
  }
}

// Loading Animation
function initCrispLoadingAnimation() {

  const container = document.querySelector(".crisp-header");
  if (!container) return;

  if (loaderTimeline) {
    loaderTimeline.kill();
    loaderTimeline = null;
  }

  resetScrollPosition();
  container.classList.add("is--loading", "is--hidden");
  setLoaderScrollLock(true);
  const heading = container.querySelectorAll(".crisp-header__h1");
  const revealImages = container.querySelectorAll(".crisp-loader__group > *");
  const isScaleUp = container.querySelectorAll(".crisp-loader__media");
  const isScaleDown = container.querySelectorAll(".crisp-loader__media .is--scale-down");
  const isRadius = container.querySelectorAll(".crisp-loader__media.is--scaling.is--radius");
  const smallElements = container.querySelectorAll(".crisp-header__top, .crisp-header__p");
  const sliderNav = container.querySelectorAll(".crisp-header__slider-nav > *");
  const navbar = document.querySelectorAll("[navbar]");
  
  
  // GSAP Timeline
  loaderTimeline = gsap.timeline({
    defaults: {
      ease: "expo.inOut",
    },
    onStart: () => {
      container.classList.remove('is--hidden');
    }
  });
  const tl = loaderTimeline;
  
  // GSAP SplitText
  let split;
  if (heading.length) {
    split = new SplitText(heading, {
      type: "words",
      mask: "words"
    });

    gsap.set(split.words, {
      yPercent: 110,
    });
  }

  if (navbar.length) {
    gsap.set(navbar, { opacity: 0 });
  }
  
  // Start of Timeline
  if (revealImages.length) {
    tl.fromTo(revealImages, {
      xPercent: 500
    }, {
      xPercent: -500,
      duration: 2.5,
      stagger: 0.05
    });
  }
  
  if (isScaleDown.length) {
    tl.to(isScaleDown, {
      scale: 0.5,
      duration: 2,
      stagger: {
        each: 0.05,
        from: "edges",
        ease: "none"
      },
      onComplete: () => {
        if (isRadius) {
          isRadius.forEach(el => el.classList.remove('is--radius'));
        }
      }
    }, "-=0.1");
  }
  
  if (isScaleUp.length) {
    tl.fromTo(isScaleUp, {
      width: "10em",
      height: "10em"
    }, {
      width: "100vw",
      height: "100dvh",
      duration: 2
    }, "< 0.5");
  }
  
  if (sliderNav.length) {
    tl.from(sliderNav, {
      yPercent: 150,
      stagger: 0.05,
      ease: "expo.out",
      duration: 1
    }, "-=0.9");
  }
  
  if (split && split.words.length) {
    tl.to(split.words, {
      yPercent: 0,
      stagger: 0.075,
      ease: "expo.out",
      duration: 1,
      delay: 1
    }, "< 0.1");
  }

  if (navbar.length) {
    tl.to(navbar, {
      opacity: 1,
      ease: "expo.out",
      duration: 1,
      delay: 1
    }, "< 0.1");
  }
  
  if (smallElements.length) {
    tl.from(smallElements, {
      opacity: 0,
      ease: "power1.inOut",
      duration: 0.2
    }, "< 0.15");
  }
  
  // Unlock scroll earlier so the content under the overlay doesn't feel "stuck".
  tl.call(() => setLoaderScrollLock(false), null, "-=0.25");

  tl.call(function () {
    container.classList.remove('is--loading');
    markLoaderSeen();
    if (window.ScrollTrigger) ScrollTrigger.refresh();
    window.dispatchEvent(new Event("loaderComplete"));
  }, null, "+=0.25");
}

function startLoader() {
  if (!shouldShowLoader()) {
    skipLoader();
    return;
  }

  // Start immediately to avoid "flash" before loader kicks in.
  // Animations will still complete when fonts arrive.
  initCrispLoadingAnimation();
}

// Initialize Crisp Loading Animation
document.addEventListener("DOMContentLoaded", startLoader);

window.addEventListener("pageshow", (event) => {
  resetScrollPosition();

  if (!event.persisted) return;

  const container = document.querySelector(".crisp-header");
  if (!container) return;

  if (!shouldShowLoader()) {
    skipLoader();
    return;
  }

  startLoader();
});

// Slideshow
function initSlideShow(el) {
  const ui = {
    el,
    slides: Array.from(el.querySelectorAll('[data-slideshow="slide"]')),
    inner: Array.from(el.querySelectorAll('[data-slideshow="parallax"]')),
    thumbs: Array.from(el.querySelectorAll('[data-slideshow="thumb"]'))
  };

  let current = 0;
  const length = ui.slides.length;
  let animating = false;
  const animationDuration = 1.5;

  ui.slides.forEach((slide, index) => slide.setAttribute('data-index', index));
  ui.thumbs.forEach((thumb, index) => thumb.setAttribute('data-index', index));

  if (!length) return;
  ui.slides[current].classList.add('is--current');
  // [WP] Nessuna miniatura nel markup: evita l'errore su ui.thumbs[0] undefined.
  if (ui.thumbs[current]) ui.thumbs[current].classList.add('is--current');

  function navigate(direction, targetIndex = null) {
    if (animating) return;
    animating = true;

    const previous = current;
    current =
      targetIndex !== null && targetIndex !== undefined
        ? targetIndex
        : direction === 1
          ? (current < length - 1 ? current + 1 : 0)
          : (current > 0 ? current - 1 : length - 1);

    const currentSlide = ui.slides[previous];
    const currentInner = ui.inner[previous];
    const upcomingSlide = ui.slides[current];
    const upcomingInner = ui.inner[current];

    gsap.timeline({
      defaults: { duration: animationDuration, ease: 'slideshow-wipe' },
      onStart() {
        upcomingSlide.classList.add('is--current');
        if (ui.thumbs[previous]) ui.thumbs[previous].classList.remove('is--current');
        if (ui.thumbs[current]) ui.thumbs[current].classList.add('is--current');
      },
      onComplete() {
        currentSlide.classList.remove('is--current');
        animating = false;
      }
    })
      .to(currentSlide, { xPercent: -direction * 100 }, 0)
      .to(currentInner, { xPercent: direction * 75 }, 0)
      .fromTo(upcomingSlide, { xPercent: direction * 100 }, { xPercent: 0 }, 0)
      .fromTo(upcomingInner, { xPercent: -direction * 75 }, { xPercent: 0 }, 0);
  }

  ui.thumbs.forEach(thumb => {
    thumb.addEventListener('click', event => {
      const targetIndex = parseInt(event.currentTarget.getAttribute('data-index'), 10);
      if (targetIndex === current || animating) return;
      const direction = targetIndex > current ? 1 : -1;
      navigate(direction, targetIndex);
    });
  });
}

// Initialize Slideshow
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-slideshow="wrap"]').forEach(wrap => initSlideShow(wrap));
});
// --- END LOADER ENABLED ---

