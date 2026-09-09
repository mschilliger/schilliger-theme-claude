(function () {
  const tracker = document.querySelector("[data-about-tracker]");
  if (!tracker) return;

  const shell = document.querySelector(".about-future-shell");
  if (!shell) return;

  const contentRoot = document.querySelector(".about-future-content");
  if (!contentRoot) return;

  const currentEl = tracker.querySelector("[data-about-current]");
  const upcomingEl = tracker.querySelector("[data-about-upcoming]");
  const scrollNav = shell.querySelector("[data-about-scrollnav]");
  if (!currentEl || !upcomingEl || !scrollNav) return;
  const eyebrowOverride = (tracker.dataset.aboutOverride || "").trim();
  const hasEyebrowOverride = Boolean(eyebrowOverride);

  const sections = Array.from(contentRoot.querySelectorAll("h1"));
  if (!sections.length) return;
  const navHeadings = Array.from(contentRoot.querySelectorAll("h1, h2"));
  if (!navHeadings.length) return;

  const getHeadingText = (heading) =>
    (heading?.textContent || "").trim();
  const slugify = (text) =>
    text
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "");
  const stickyTop = parseFloat(getComputedStyle(tracker).top) || 0;
  const topBarHeight =
    parseFloat(getComputedStyle(document.documentElement).getPropertyValue("--topbar-h")) || 0;
  let trackerStartY = 0;
  let activeIndex = 0;
  let isScrolled = false;

  navHeadings.forEach((heading, idx) => {
    if (heading.id) return;
    const base = slugify(getHeadingText(heading)) || `section-${idx + 1}`;
    let candidate = `about-${base}`;
    let suffix = 2;
    while (document.getElementById(candidate)) {
      candidate = `about-${base}-${suffix}`;
      suffix += 1;
    }
    heading.id = candidate;
  });

  const recalcTrackerStart = () => {
    shell.classList.remove("is-scrolled");
    trackerStartY = tracker.getBoundingClientRect().top + window.scrollY;
  };

  const buildTopList = (index) => {
    upcomingEl.innerHTML = "";
    sections.forEach((heading, idx) => {
      if (idx === index) return;
      const item = document.createElement("a");
      item.className = "about-future-eyebrow-item";
      item.href = `#${heading.id}`;
      item.dataset.sectionId = heading.id;
      item.textContent = getHeadingText(heading);
      upcomingEl.appendChild(item);
    });
  };

  const buildScrollNav = () => {
    scrollNav.innerHTML = "";
    navHeadings.forEach((heading) => {
      const item = document.createElement("a");
      item.className = "about-future-scrollnav-item";
      if (heading.tagName.toLowerCase() === "h2") {
        item.classList.add("is-h2");
      }
      item.href = `#${heading.id}`;
      item.dataset.sectionId = heading.id;
      item.textContent = getHeadingText(heading);
      scrollNav.appendChild(item);
    });
  };

  const paintActive = () => {
    const activeHeading = sections[activeIndex] || sections[0];
    const activeTitle = getHeadingText(activeHeading) || getHeadingText(sections[0]);
    currentEl.textContent = hasEyebrowOverride ? eyebrowOverride : activeTitle;
    currentEl.href = activeHeading?.id ? `#${activeHeading.id}` : "#";
    if (hasEyebrowOverride && !isScrolled) {
      upcomingEl.innerHTML = "";
    } else {
      buildTopList(activeIndex);
    }

    const navMarker = window.scrollY + topBarHeight + stickyTop + 28;
    let activeNavHeading = navHeadings[0];
    navHeadings.forEach((heading) => {
      if (heading.offsetTop <= navMarker) {
        activeNavHeading = heading;
      }
    });
    const activeId = activeNavHeading?.id || "";
    const scrollLinks = scrollNav.querySelectorAll(".about-future-scrollnav-item");
    scrollLinks.forEach((link) => {
      link.classList.toggle("is-active", link.dataset.sectionId === activeId);
    });
  };

  const detectState = () => {
    const y = window.scrollY + stickyTop;
    const nextScrolled = isScrolled ? y > trackerStartY - 4 : y > trackerStartY + 4;
    if (nextScrolled !== isScrolled) {
      isScrolled = nextScrolled;
      shell.classList.toggle("is-scrolled", isScrolled);
      tracker.classList.toggle("is-hidden", isScrolled);
    }

    const marker = window.scrollY + topBarHeight + stickyTop + 28;
    let nextActive = 0;

    sections.forEach((heading, idx) => {
      if (heading.offsetTop <= marker) {
        nextActive = idx;
      }
    });

    if (nextActive !== activeIndex) {
      activeIndex = nextActive;
      paintActive();
    }
  };

  const onNavClick = (event) => {
    const link = event.target.closest("a[href^='#']");
    if (!link) return;
    const targetId = link.getAttribute("href").slice(1);
    const target = targetId ? document.getElementById(targetId) : null;
    if (!target) return;

    event.preventDefault();
    target.scrollIntoView({ behavior: "smooth", block: "start" });
  };

  tracker.addEventListener("click", onNavClick);
  scrollNav.addEventListener("click", onNavClick);

  buildScrollNav();
  recalcTrackerStart();
  detectState();
  paintActive();
  window.addEventListener("scroll", () => {
    detectState();
    paintActive();
  }, { passive: true });
  window.addEventListener("resize", () => {
    recalcTrackerStart();
    detectState();
    paintActive();
  });
})();
