document.querySelectorAll(".demo-card").forEach((card) => {
    const toggle = card.querySelector(".card-toggle");
    const detail = card.querySelector(".card-detail");
    const plusVertical = card.querySelector(".plus-line-v");

    if (!toggle || !detail) return;

    gsap.set(detail, {
      autoAlpha: detail.classList.contains("is-on") ? 1 : 0
    });

    gsap.set(plusVertical, {
      rotate: detail.classList.contains("is-on") ? 0 : 90,
      autoAlpha: detail.classList.contains("is-on") ? 0 : 1
    });

    toggle.addEventListener("click", function (e) {
      e.preventDefault();

      const isOpen = detail.classList.contains("is-on");

      gsap.killTweensOf([detail, plusVertical]);

      if (isOpen) {
        gsap.to(detail, {
          autoAlpha: 0,
          y: 12,
          duration: 0.3,
          ease: "power2.out",
          onComplete: () => {
            detail.classList.remove("is-on");
          }
        });

        gsap.to(plusVertical, {
          rotate: 90,
          autoAlpha: 1,
          duration: 0.3,
          ease: "power2.out"
        });

      } else {
        detail.classList.add("is-on");

        gsap.fromTo(detail,
          {
            autoAlpha: 0,
            y: 12
          },
          {
            autoAlpha: 1,
            y: 0,
            duration: 0.3,
            ease: "power2.out"
          }
        );

        gsap.to(plusVertical, {
          rotate: 0,
          autoAlpha: 0,
          duration: 0.3,
          ease: "power2.out"
        });
      }
    });
  });