
gsap.registerPlugin(Draggable, InertiaPlugin);

function initBasicGSAPSlider() {
  document.querySelectorAll('[data-gsap-slider-init]').forEach(root => {
    if (root._sliderDraggable) root._sliderDraggable.kill();

    const collection = root.querySelector('[data-gsap-slider-collection]');
    const track      = root.querySelector('[data-gsap-slider-list]');
    const items      = Array.from(root.querySelectorAll('[data-gsap-slider-item]'));
    let paginationContainer = root.querySelector('[data-gsap-slider-pagination]');
    if (!paginationContainer) {
      paginationContainer = document.createElement('div');
      paginationContainer.setAttribute('data-gsap-slider-pagination', '');
      root.appendChild(paginationContainer);
    } else if (collection && paginationContainer.previousElementSibling !== collection) {
      root.appendChild(paginationContainer);
    }
    paginationContainer.innerHTML = '';
    collection.style.removeProperty('width');

    // Inject aria attributes
    root.setAttribute('role','region');
    root.setAttribute('aria-roledescription','carousel');
    root.setAttribute('aria-label','Slider');
    collection.setAttribute('role','group');
    collection.setAttribute('aria-roledescription','Slides List');
    collection.setAttribute('aria-label','Slides');
    items.forEach((slide,i) => {
      slide.setAttribute('role','group');
      slide.setAttribute('aria-roledescription','Slide');
      slide.setAttribute('aria-label',`Slide ${i+1} of ${items.length}`);
      slide.setAttribute('aria-hidden','true');
      slide.setAttribute('aria-selected','false');
      slide.setAttribute('tabindex','-1');
    });
    // Determine if slider runs
    const styles      = getComputedStyle(root);
    const statusVar   = styles.getPropertyValue('--slider-status').trim();
    let   spvVar      = parseFloat(styles.getPropertyValue('--slider-spv'));
    const rect        = items[0].getBoundingClientRect();
    const marginRight = parseFloat(getComputedStyle(items[0]).marginRight);
    const slideW      = rect.width + marginRight;
    if (isNaN(spvVar)) {
      spvVar = collection.clientWidth / slideW;
    }
    const spv           = Math.max(1, Math.min(spvVar, items.length));
    const sliderEnabled = statusVar==='on' && spv < items.length;
    root.setAttribute('data-gsap-slider-status', sliderEnabled ? 'active' : 'not-active');

    if (!sliderEnabled) {
      // Teardown when disabled
      track.removeAttribute('style');
      track.onmouseenter = null;
      track.onmouseleave = null;
      track.removeAttribute('data-gsap-slider-list-status');
      root.removeAttribute('role');
      root.removeAttribute('aria-roledescription');
      root.removeAttribute('aria-label');
      collection.removeAttribute('role');
      collection.removeAttribute('aria-roledescription');
      collection.removeAttribute('aria-label');
      items.forEach(slide => {
        slide.removeAttribute('role');
        slide.removeAttribute('aria-roledescription');
        slide.removeAttribute('aria-label');
        slide.removeAttribute('aria-hidden');
        slide.removeAttribute('aria-selected');
        slide.removeAttribute('tabindex');
        slide.removeAttribute('data-gsap-slider-item-status');
      });
      paginationContainer.innerHTML = '';
      paginationContainer.removeAttribute('role');
      paginationContainer.removeAttribute('aria-label');
      return;
    }

    // Track hover state
    track.onmouseenter = () => {
      track.setAttribute('data-gsap-slider-list-status','grab');
    };
    track.onmouseleave = () => {
      track.removeAttribute('data-gsap-slider-list-status');
    };

    function getViewportWidth() {
      return document.documentElement.clientWidth - collection.getBoundingClientRect().left;
    }

    //Ccalculate bounds and snap points
    const vw        = getViewportWidth();
    const tw        = track.scrollWidth;
    const maxScroll = Math.max(tw - vw, 0);
    const minX      = -maxScroll;
    const maxX      = 0;
    const snapPoints = items.map((_, i) => Math.max(-i * slideW, minX));

    // Collapse duplicate snap positions at the end (when spv > 1, extra slides
    // clamp to minX and would create bullets that don't move the track).
    const pages = snapPoints.reduce((acc, x, slideIndex) => {
      if (!acc.length || acc[acc.length - 1].x !== x) {
        acc.push({ x, slideIndex });
      }
      return acc;
    }, []);

    let activeIndex    = 0;
    const setX         = gsap.quickSetter(track,'x','px');
    let collectionRect = collection.getBoundingClientRect();
    const bullets      = [];

    paginationContainer.setAttribute('role', 'tablist');
    paginationContainer.setAttribute('aria-label', 'Slider pagination');

    pages.forEach((page, i) => {
      const bullet = document.createElement('button');
      bullet.type = 'button';
      bullet.setAttribute('data-gsap-slider-bullet', '');
      bullet.setAttribute('role', 'tab');
      bullet.setAttribute('aria-label', `Go to slide ${i + 1} of ${pages.length}`);
      bullet.setAttribute('aria-selected', 'false');
      bullet.addEventListener('click', () => {
        gsap.to(track, {
          duration: 0.4,
          x: page.x,
          onUpdate: () => updateStatus(gsap.getProperty(track, 'x'))
        });
      });
      paginationContainer.appendChild(bullet);
      bullets.push(bullet);
    });

    function getActiveIndex(x) {
      const calcX = x > maxX ? maxX : (x < minX ? minX : x);
      let index = 0;
      let closestDist = Infinity;

      pages.forEach((page, i) => {
        const dist = Math.abs(page.x - calcX);
        if (dist < closestDist) {
          closestDist = dist;
          index = i;
        }
      });

      return pages[index].slideIndex;
    }

    function updateStatus(x) {
      if (x > maxX || x < minX) {
        return;
      }

      activeIndex = getActiveIndex(x);

      // Update Slide Attributes
      items.forEach((slide,i) => {
        const r             = slide.getBoundingClientRect();
        const leftEdge      = r.left - collectionRect.left;
        const rightEdge     = leftEdge + r.width;
        const viewportWidth = getViewportWidth();
        const inView        = rightEdge > 0 && leftEdge < viewportWidth;
        const status        = i === activeIndex ? 'active' : inView ? 'inview' : 'not-active';

        slide.setAttribute('data-gsap-slider-item-status', status);
        slide.setAttribute('aria-selected',    i === activeIndex ? 'true' : 'false');
        slide.setAttribute('aria-hidden',      inView ? 'false' : 'true');
        slide.setAttribute('tabindex',         i === activeIndex ? '0'    : '-1');
      });

      // Update pagination
      const activePageIndex = pages.findIndex(page => page.slideIndex === activeIndex);
      bullets.forEach((bullet, i) => {
        const isActive = i === activePageIndex;
        bullet.setAttribute('data-gsap-slider-bullet-status', isActive ? 'active' : 'not-active');
        bullet.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
    }

    // Initialize Draggable
    root._sliderDraggable = Draggable.create(track, {
      type: 'x',
      // cursor: 'inherit',
      // activeCursor: 'inherit',
      inertia: true,
      bounds: {minX, maxX},
      throwResistance: 2000,
      dragResistance: 0.05,
      maxDuration: 0.6,
      minDuration: 0.2,
      edgeResistance: 0.75,
      snap: {x: snapPoints, duration: 0.4},
      onPress() {
        track.setAttribute('data-gsap-slider-list-status','grabbing');
        collectionRect = collection.getBoundingClientRect();
      },
      onDrag() {
        setX(this.x);
        updateStatus(this.x);
      },
      onThrowUpdate() {
        setX(this.x);
        updateStatus(this.x);
      },
      onThrowComplete() {
        setX(this.endX);
        updateStatus(this.endX);
        track.setAttribute('data-gsap-slider-list-status','grab');
      },
      onRelease() {
        setX(this.x);
        updateStatus(this.x);
        track.setAttribute('data-gsap-slider-list-status','grab');
      }
    })[0];

    // Initial state
    setX(0);
    updateStatus(0);
  });
}

// Debouncer: For resizing the window
function debounceOnWidthChange(fn, ms) {
  let last = innerWidth, timer;
  return function(...args) {
    clearTimeout(timer);
    timer = setTimeout(() => {
      if (innerWidth !== last) {
        last = innerWidth;
        fn.apply(this, args);
      }
    }, ms);
  };
}

window.addEventListener('resize', debounceOnWidthChange(initBasicGSAPSlider, 200));

// Initialize Basic GSAP Slider
document.addEventListener('DOMContentLoaded', function() {
  initBasicGSAPSlider();
});