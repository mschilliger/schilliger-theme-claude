(function () {
  const toggle = document.getElementById("mobile-toggle");
  if (!toggle) return;

  toggle.addEventListener("click", () => {
    document.body.classList.toggle("mobile-open");
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      document.body.classList.remove("mobile-open");
    }
  });
})();
