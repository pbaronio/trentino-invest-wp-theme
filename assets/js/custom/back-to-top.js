gsap.registerPlugin(ScrollTrigger);

function initBackToTop() {
  const buttonWrap = document.querySelector('[data-back-to-top="wrap"]');
  const button = document.querySelector('[data-back-to-top="button"]');
  if (!button || !buttonWrap) return;
  
  // The minimum distance the page must be scrolled (in VH) for the button to appear  
  let minimumScrollDistance = 50;

  // Un-do the initial CSS styling to hide the wrapper
  gsap.set(buttonWrap, { autoAlpha: 1 });
  
  // Hide the button itself
  gsap.set(button,{autoAlpha: 0});

  ScrollTrigger.create({
    trigger: document.body,
    start: `top top-=${minimumScrollDistance}%`,
    onEnter: () => {
      gsap.fromTo(button,{
        autoAlpha: 0,
        rotate:-65,
        scale: 0.4,
      },{
        autoAlpha: 1,
        rotate:0,
        scale: 1,        
        duration: 0.45,
        ease:"power4.out"
      })
    },
    onLeaveBack: () => {
      gsap.to(button,{
        autoAlpha: 0,
        rotate:-65,
        scale: 0.6,
        duration:0.4,
        ease:"power4.out"
      })
    },
  });

  button.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  });
}

// Initialize Back to Top Button
document.addEventListener("DOMContentLoaded", ()=>{
  initBackToTop();
});