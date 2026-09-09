(() => {
  const root = document.querySelector("[data-lib-page]");
  if (!root) return;

  const filterEl = root.querySelector("[data-lib-filter]");
  const filterWrap = root.querySelector("[data-lib-filter-wrap]");
  const typeButtons = filterEl ? Array.from(filterEl.querySelectorAll("[data-lib-type]")) : [];
  const collectionsBtn = filterEl ? filterEl.querySelector("[data-lib-collections]") : null;
  const subfilterEl = root.querySelector("[data-lib-subfilter-tags]");
  const tagHeadingEl = root.querySelector("[data-lib-tag-heading]");
  const tagDescriptionEl = root.querySelector("[data-lib-tag-description]");
  const yearsWrap = root.querySelector("[data-lib-years-wrap]");
  const flatWrap = root.querySelector("[data-lib-tag-flat-wrap]");
  const flatGrid = root.querySelector("[data-lib-grid-flat]");

  const yearGrids = new Map();
  let prevTagSlug = "";

  const buildYearGrids = () => {
    yearGrids.clear();
    root.querySelectorAll("[data-lib-year]").forEach((section) => {
      const y = section.getAttribute("data-year");
      const grid = section.querySelector("[data-lib-grid]");
      if (y && grid) {
        yearGrids.set(y, grid);
      }
    });
  };

  const moveAllToFlatGrid = () => {
    if (!flatGrid || !yearsWrap) return;
    root.querySelectorAll("[data-lib-year]").forEach((section) => {
      const grid = section.querySelector("[data-lib-grid]");
      if (!grid) return;
      Array.from(grid.querySelectorAll("[data-lib-item]")).forEach((item) => {
        flatGrid.appendChild(item);
      });
    });
  };

  const restoreItemsToYearGrids = () => {
    if (!flatGrid) return;
    Array.from(flatGrid.querySelectorAll("[data-lib-item]")).forEach((item) => {
      const y = item.getAttribute("data-lib-year-key");
      const grid = y ? yearGrids.get(y) : null;
      if (grid) {
        grid.appendChild(item);
      }
    });
  };

  buildYearGrids();

  const closeAll = () => {
    root.querySelectorAll("[data-lib-item].is-open").forEach((item) => {
      item.classList.remove("is-open");
      item.classList.remove("is-menu");
      const toggle = item.querySelector("[data-lib-toggle]");
      if (toggle) toggle.setAttribute("aria-expanded", "false");
    });
  };

  const getActiveTypes = () => {
    const active = typeButtons
      .filter((b) => b.getAttribute("aria-pressed") === "true")
      .map((b) => b.getAttribute("data-lib-type"))
      .filter(Boolean);
    return new Set(active);
  };

  /** Tag-Filter nur bei aktivem Collections + gewähltem Tag. */
  const getSelectedTagSlug = () => {
    if (!collectionsBtn || collectionsBtn.getAttribute("aria-pressed") !== "true") {
      return "";
    }
    if (!filterWrap || !filterWrap.classList.contains("is-collections-open")) {
      return "";
    }
    const active = subfilterEl ? subfilterEl.querySelector(".lib-tag-btn.is-active") : null;
    return active ? active.getAttribute("data-lib-tag") || "" : "";
  };

  const updateYearVsTagHeading = (tagSlug) => {
    const yearTitles = root.querySelectorAll(".lib-year-title");
    if (!tagHeadingEl) {
      yearTitles.forEach((h) => {
        h.hidden = false;
      });
      return;
    }

    if (tagSlug) {
      const btn = subfilterEl ? subfilterEl.querySelector(".lib-tag-btn.is-active") : null;
      const label = btn ? btn.getAttribute("data-lib-tag-name") || tagSlug : tagSlug;
      tagHeadingEl.textContent = label;
      tagHeadingEl.hidden = false;
      tagHeadingEl.setAttribute("aria-hidden", "false");

      if (tagDescriptionEl) {
        const desc = btn ? btn.getAttribute("data-lib-tag-description") || "" : "";
        const descTrim = desc.trim();
        tagDescriptionEl.textContent = descTrim;
        tagDescriptionEl.hidden = !descTrim;
        tagDescriptionEl.setAttribute("aria-hidden", descTrim ? "false" : "true");
      }

      yearTitles.forEach((h) => {
        h.hidden = true;
      });
    } else {
      tagHeadingEl.textContent = "";
      tagHeadingEl.hidden = true;
      tagHeadingEl.setAttribute("aria-hidden", "true");

      if (tagDescriptionEl) {
        tagDescriptionEl.textContent = "";
        tagDescriptionEl.hidden = true;
        tagDescriptionEl.setAttribute("aria-hidden", "true");
      }

      yearTitles.forEach((h) => {
        h.hidden = false;
      });
    }
  };

  const setTagLayoutMode = (tagSlug) => {
    const inTagMode = !!tagSlug;
    if (yearsWrap) {
      yearsWrap.hidden = inTagMode;
    }
    if (flatWrap) {
      flatWrap.hidden = !inTagMode;
      flatWrap.setAttribute("aria-hidden", inTagMode ? "false" : "true");
    }
  };

  const applyFilter = () => {
    if (!typeButtons.length) return;

    const tagSlug = getSelectedTagSlug();

    if (prevTagSlug && !tagSlug) {
      restoreItemsToYearGrids();
      buildYearGrids();
    } else if (!prevTagSlug && tagSlug) {
      buildYearGrids();
      moveAllToFlatGrid();
    }

    prevTagSlug = tagSlug;

    const active = getActiveTypes();

    root.querySelectorAll("[data-lib-item][data-lib-media]").forEach((item) => {
      const t = item.getAttribute("data-lib-media") || "book";
      const typesOk = active.size === 0 || active.has(t);

      let tagOk = true;
      if (tagSlug) {
        const tags = (item.getAttribute("data-lib-tags") || "")
          .split(",")
          .map((s) => s.trim())
          .filter(Boolean);
        tagOk = tags.includes(tagSlug);
      }

      item.hidden = !(typesOk && tagOk);
    });

    if (tagSlug) {
      setTagLayoutMode(tagSlug);
      updateYearVsTagHeading(tagSlug);
    } else {
      setTagLayoutMode("");
      updateYearVsTagHeading("");
      root.querySelectorAll("[data-lib-year]").forEach((yearBlock) => {
        const anyVisible = Array.from(yearBlock.querySelectorAll("[data-lib-item]")).some((i) => !i.hidden);
        yearBlock.hidden = !anyVisible;
      });
    }

    closeAll();
  };

  const closeCollectionsSubfilter = () => {
    if (filterWrap) {
      filterWrap.classList.remove("is-collections-open");
    }
    if (subfilterEl) {
      subfilterEl.querySelectorAll(".lib-tag-btn.is-active").forEach((b) => {
        b.classList.remove("is-active");
        b.setAttribute("aria-pressed", "false");
      });
    }
    if (collectionsBtn) {
      collectionsBtn.setAttribute("aria-pressed", "false");
      collectionsBtn.classList.remove("is-active");
      collectionsBtn.setAttribute("aria-expanded", "false");
    }
  };

  root.addEventListener("click", (e) => {
    const closeBtn = e.target.closest("[data-lib-close]");
    if (closeBtn) {
      const item = closeBtn.closest("[data-lib-item]");
      if (!item) return;
      item.classList.remove("is-open");
      const toggle = item.querySelector("[data-lib-toggle]");
      if (toggle) toggle.setAttribute("aria-expanded", "false");
      return;
    }

    const collectionsClick = e.target.closest("[data-lib-collections]");
    if (collectionsClick && filterEl && filterEl.contains(collectionsClick)) {
      const willOpen = collectionsClick.getAttribute("aria-pressed") !== "true";
      if (willOpen) {
        collectionsClick.setAttribute("aria-pressed", "true");
        collectionsClick.classList.add("is-active");
        collectionsClick.setAttribute("aria-expanded", "true");
        if (filterWrap) {
          filterWrap.classList.add("is-collections-open");
        }
      } else {
        closeCollectionsSubfilter();
      }
      applyFilter();
      return;
    }

    const tagBtn = e.target.closest("[data-lib-tag]");
    if (tagBtn && subfilterEl && subfilterEl.contains(tagBtn)) {
      const slug = tagBtn.getAttribute("data-lib-tag") || "";
      const isOn = tagBtn.classList.contains("is-active");
      subfilterEl.querySelectorAll(".lib-tag-btn").forEach((b) => {
        b.classList.remove("is-active");
        b.setAttribute("aria-pressed", "false");
      });
      if (!isOn && slug) {
        tagBtn.classList.add("is-active");
        tagBtn.setAttribute("aria-pressed", "true");
      }
      applyFilter();
      return;
    }

    const filterBtn = e.target.closest("[data-lib-type]");
    if (filterBtn && filterEl && filterEl.contains(filterBtn)) {
      const isPressed = filterBtn.getAttribute("aria-pressed") === "true";
      filterBtn.setAttribute("aria-pressed", isPressed ? "false" : "true");
      filterBtn.classList.toggle("is-active", !isPressed);
      applyFilter();
      return;
    }

    const toggle = e.target.closest("[data-lib-toggle]");
    if (!toggle) return;
    const item = toggle.closest("[data-lib-item]");
    if (!item) return;

    const detailsBtn = e.target.closest("[data-lib-details]");
    const isMobile = window.matchMedia && window.matchMedia("(max-width: 720px)").matches;

    if (detailsBtn && !isMobile) {
      const isOpen = item.classList.contains("is-open");
      closeAll();
      item.classList.toggle("is-open", !isOpen);
      item.classList.remove("is-menu");
      return;
    }

    const isOpen = item.classList.contains("is-open");

    closeAll();

    if (isMobile) {
      if (!isOpen) {
        item.classList.add("is-open");
        toggle.setAttribute("aria-expanded", "true");
        item.scrollIntoView({ block: "nearest", behavior: "smooth" });
      }
      return;
    }

    item.classList.toggle("is-menu", !item.classList.contains("is-menu"));
  });

  root.addEventListener("keydown", (e) => {
    const toggle = e.target.closest && e.target.closest("[data-lib-toggle]");
    if (!toggle) return;
    if (e.key !== "Enter" && e.key !== " ") return;
    e.preventDefault();
    toggle.click();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    closeAll();
  });

  document.addEventListener("click", (e) => {
    if (root.contains(e.target) && e.target.closest("[data-lib-item]")) return;
    closeAll();
  });

  applyFilter();
})();
