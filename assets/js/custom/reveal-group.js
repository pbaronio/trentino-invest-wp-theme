gsap.registerPlugin(ScrollTrigger);

function buildRevealGroupConfig(groupEl, prefersReduced) {
  // Config from attributes or defaults (group-level)
  const groupStaggerSec = (parseFloat(groupEl.getAttribute('data-stagger')) || 100) / 1000; // ms → sec
  const groupDistance = groupEl.getAttribute('data-distance') || '2em';
  const triggerStart = groupEl.getAttribute('data-start') || 'top 80%';

  const animDuration = 0.8;
  const animEase = "power4.inOut";

  const directChildren = Array.from(groupEl.children).filter(el => el.nodeType === 1);
  const hasDirectChildren = directChildren.length > 0;

  if (prefersReduced) {
    return {
      groupEl,
      prefersReduced: true,
      hasDirectChildren,
      groupStaggerSec,
      groupDistance,
      triggerStart,
      animDuration,
      animEase,
      slots: [],
    };
  }

  // Build animation slots: item or nested (deep layers allowed)
  const slots = [];

  if (hasDirectChildren) {
    directChildren.forEach(child => {
      const nestedGroup = child.matches('[data-reveal-group-nested]')
        ? child
        : child.querySelector(':scope [data-reveal-group-nested]');

      if (nestedGroup) {
        const includeParent =
          child.getAttribute('data-ignore') !== 'true' &&
          (
            child.getAttribute('data-ignore') === 'false' ||
            nestedGroup.getAttribute('data-ignore') === 'false'
          );

        const nestedChildren = Array.from(nestedGroup.children).filter(
          el => el.nodeType === 1 && el.getAttribute('data-ignore') !== 'true'
        );

        slots.push({
          type: 'nested',
          parentEl: child,
          nestedEl: nestedGroup,
          includeParent,
          nestedChildren
        });
      } else {
        if (child.getAttribute('data-ignore') === 'true') return;
        slots.push({ type: 'item', el: child });
      }
    });
  }

  return {
    groupEl,
    prefersReduced: false,
    hasDirectChildren,
    groupStaggerSec,
    groupDistance,
    triggerStart,
    animDuration,
    animEase,
    slots,
  };
}

function applyRevealGroupInitialState(cfg) {
  if (cfg.prefersReduced) {
    // Reduced motion: show immediately
    gsap.set(cfg.groupEl, { clearProps: 'all', y: 0, autoAlpha: 1 });
    return;
  }

  if (!cfg.hasDirectChildren) {
    // If no direct children, animate the group element itself
    gsap.set(cfg.groupEl, { y: cfg.groupDistance, autoAlpha: 0 });
    return;
  }

  // Initial hidden state (items + nested children)
  cfg.slots.forEach(slot => {
    if (slot.type === 'item') {
      // If the element itself is a nested group, force group distance
      const isNestedSelf = slot.el.matches('[data-reveal-group-nested]');
      const d = isNestedSelf ? cfg.groupDistance : (slot.el.getAttribute('data-distance') || cfg.groupDistance);
      gsap.set(slot.el, { y: d, autoAlpha: 0 });
    } else {
      // Parent follows the group's distance when included
      if (slot.includeParent) gsap.set(slot.parentEl, { y: cfg.groupDistance, autoAlpha: 0 });
      // Children use nested group's own distance (fallback to group distance)
      const nestedD = slot.nestedEl.getAttribute('data-distance') || cfg.groupDistance;
      slot.nestedChildren.forEach(target => gsap.set(target, { y: nestedD, autoAlpha: 0 }));
    }
  });

  // Extra safety: if a nested parent is included, re-assert its distance to the group's value
  cfg.slots.forEach(slot => {
    if (slot.type === 'nested' && slot.includeParent) {
      gsap.set(slot.parentEl, { y: cfg.groupDistance });
    }
  });
}

function createRevealGroupTrigger(cfg) {
  if (cfg.prefersReduced) return;

  if (!cfg.hasDirectChildren) {
    ScrollTrigger.create({
      trigger: cfg.groupEl,
      start: cfg.triggerStart,
      once: true,
      onEnter: () => gsap.to(cfg.groupEl, {
        y: 0,
        autoAlpha: 1,
        duration: cfg.animDuration,
        ease: cfg.animEase,
        onComplete: () => gsap.set(cfg.groupEl, { clearProps: 'all' }),
      }),
    });
    return;
  }

  ScrollTrigger.create({
    trigger: cfg.groupEl,
    start: cfg.triggerStart,
    once: true,
    onEnter: () => {
      const tl = gsap.timeline();

      cfg.slots.forEach((slot, slotIndex) => {
        const slotTime = slotIndex * cfg.groupStaggerSec;

        if (slot.type === 'item') {
          tl.to(slot.el, {
            y: 0,
            autoAlpha: 1,
            duration: cfg.animDuration,
            ease: cfg.animEase,
            onComplete: () => gsap.set(slot.el, { clearProps: 'all' }),
          }, slotTime);
          return;
        }

        // Nested slots
        if (slot.includeParent) {
          tl.to(slot.parentEl, {
            y: 0,
            autoAlpha: 1,
            duration: cfg.animDuration,
            ease: cfg.animEase,
            onComplete: () => gsap.set(slot.parentEl, { clearProps: 'all' }),
          }, slotTime);
        }

        const nestedMs = parseFloat(slot.nestedEl.getAttribute('data-stagger'));
        const nestedStaggerSec = isNaN(nestedMs) ? cfg.groupStaggerSec : nestedMs / 1000;

        slot.nestedChildren.forEach((nestedChild, nestedIndex) => {
          tl.to(nestedChild, {
            y: 0,
            autoAlpha: 1,
            duration: cfg.animDuration,
            ease: cfg.animEase,
            onComplete: () => gsap.set(nestedChild, { clearProps: 'all' }),
          }, slotTime + nestedIndex * nestedStaggerSec);
        });
      });
    },
  });
}

// Initialize Elements Reveal on Scroll (after loader)
document.addEventListener("DOMContentLoaded", () => {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const groupEls = Array.from(document.querySelectorAll('[data-reveal-group]'));
  const cfgs = groupEls.map(groupEl => buildRevealGroupConfig(groupEl, prefersReduced));

  const applyInitial = () => cfgs.forEach(applyRevealGroupInitialState);
  const createTriggers = () => cfgs.forEach(createRevealGroupTrigger);

  // Key fix: apply the initial hidden state immediately (so we don't "flash"
  // the content and then hide it when loaderComplete arrives).
  if (document.querySelector(".crisp-header")) {
    applyInitial();
    window.whenLoaderReady(createTriggers);
  } else {
    applyInitial();
    createTriggers();
  }
});