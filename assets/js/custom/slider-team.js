function initSwiperSlider() {
  if (typeof Swiper === "undefined") {
    console.warn("[slider-team] Swiper non è caricato. Aggiungi la libreria Swiper prima di slider-team.js.");
    return;
  }

  document.querySelectorAll("[data-swiper-group]").forEach((swiperGroup) => {
    const swiperSliderWrap = swiperGroup.querySelector("[data-swiper-wrap]");
    if (!swiperSliderWrap) return;

    const prevButton = swiperGroup.querySelector("[data-swiper-prev]");
    const nextButton = swiperGroup.querySelector("[data-swiper-next]");
    let paginationEl = swiperGroup.querySelector(".swiper-pagination");

    if (!paginationEl) {
      paginationEl = document.createElement("div");
      paginationEl.className = "swiper-pagination";
      swiperGroup.appendChild(paginationEl);
    }

    swiperSliderWrap.setAttribute("data-lenis-prevent", "");

    [prevButton, nextButton].forEach((button) => {
      if (!button) return;
      button.setAttribute("type", "button");
      button.addEventListener("click", (event) => event.preventDefault());
    });

    new Swiper(swiperSliderWrap, {
      slidesPerView: "auto",
      slidesPerGroup: 1,
      spaceBetween: 0,
      speed: 600,
      grabCursor: true,
      simulateTouch: true,
      allowTouchMove: true,
      touchRatio: 1,
      touchAngle: 45,
      threshold: 5,
      longSwipes: true,
      followFinger: true,
      resistance: true,
      resistanceRatio: 0.85,
      watchOverflow: false,
      navigation: {
        nextEl: nextButton,
        prevEl: prevButton,
      },
      pagination: {
        el: paginationEl,
        type: "bullets",
        clickable: true,
        dynamicBullets: false,
      },
      keyboard: {
        enabled: true,
        onlyInViewport: false,
      },
    });
  });
}

function onReady(callback) {
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", callback);
  } else {
    callback();
  }
}

onReady(initSwiperSlider);
