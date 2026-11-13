// Lenis
const lenis = new Lenis({
  autoRaf: true,
});

window.lenis = lenis;

// Stop scrolling as soon as the loader container exists.
// We can't rely on `.is--loading` being set yet (race vs `loader.js`).
if (document.querySelector(".crisp-header")) {
  lenis.stop();
}