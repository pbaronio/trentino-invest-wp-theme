document.addEventListener("DOMContentLoaded", () => {
  const start = () => {
    const headings = document.querySelectorAll('[data-split="heading"]');
    headings.forEach(heading => {
      SplitText.create(heading, {
        type: "lines",
        autoSplit: true,
        mask: "lines",
        onSplit(instance) {
          return gsap.from(instance.lines, {
            duration: 0.8,
            yPercent: 110,
            stagger: 0.1,
            ease: "expo.out",
          });
        }
      });
    });
  };

  // Avoid race conditions: wait for the loader to finish if the loader exists.
  if (document.querySelector(".crisp-header")) {
    window.whenLoaderReady(start);
  } else {
    start();
  }
});