(function () {
  const mobileToggle = document.getElementById("mobile-toggle");
  const menuLinks = document.querySelectorAll(".nav-main a");
  const mobileQuery = window.matchMedia("(max-width: 980px)");
  const topbar = document.querySelector(".site-topbar.header-variant-c");
  const defaultHeaderBg = "#d1e2dd";
  let lastY = window.scrollY;
  let ticking = false;

  if (mobileToggle) {
    mobileToggle.addEventListener("click", () => {
      document.body.classList.toggle("mobile-open");
    });
  }

  menuLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (mobileQuery.matches) document.body.classList.remove("mobile-open");
    });
  });

  const onResize = () => {
    if (!mobileQuery.matches) document.body.classList.remove("mobile-open");
  };
  window.addEventListener("resize", onResize);

  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(() => {
      const currentY = window.scrollY;
      const delta = currentY - lastY;
      if (currentY < 80 || delta < -6) {
        document.body.classList.remove("nav-hidden");
      } else if (delta > 6) {
        document.body.classList.add("nav-hidden");
      }
      document.body.classList.toggle("header-scroll-compact", currentY > 80);
      lastY = currentY;
      ticking = false;
    });
  };
  window.addEventListener("scroll", onScroll, { passive: true });

  if (topbar) {
    const sections = Array.from(document.querySelectorAll("main section"));
    const resolveSectionBg = (section) => {
      const color = window.getComputedStyle(section).backgroundColor;
      if (!color || color === "rgba(0, 0, 0, 0)" || color === "transparent") return "";
      return color;
    };

    const syncHeaderBg = () => {
      if (window.scrollY <= 8 || !sections.length) {
        topbar.style.setProperty("--header-dynamic-bg", defaultHeaderBg);
        return;
      }

      const markerY = topbar.getBoundingClientRect().height + 8;
      let activeColor = "";
      for (const section of sections) {
        const rect = section.getBoundingClientRect();
        if (rect.top <= markerY && rect.bottom > markerY) {
          activeColor = resolveSectionBg(section);
          break;
        }
      }

      topbar.style.setProperty("--header-dynamic-bg", activeColor || defaultHeaderBg);
    };

    window.addEventListener("scroll", syncHeaderBg, { passive: true });
    window.addEventListener("resize", syncHeaderBg);
    syncHeaderBg();
  }
})();
