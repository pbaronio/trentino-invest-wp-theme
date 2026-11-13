document.addEventListener("DOMContentLoaded", function () {
    const navButton = document.querySelector(".w-nav-button");
    const menuText = document.querySelector(".menu_text");

    if (!navButton || !menuText) return;

    navButton.addEventListener("click", function () {
      setTimeout(function () {
        if (navButton.classList.contains("w--open")) {
          menuText.textContent = "Chiudi";
        } else {
          menuText.textContent = "Menu";
        }
      }, 50);
    });
  });