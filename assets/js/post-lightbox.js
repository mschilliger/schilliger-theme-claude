(function () {
  const root = document.querySelector(".single-post-wrap .post-body");
  if (!root) return;

  const imageNodes = Array.from(root.querySelectorAll("img"));
  const images = imageNodes
    .map((img) => {
      const src =
        img.getAttribute("data-full-src") ||
        img.getAttribute("data-large-file") ||
        img.currentSrc ||
        img.getAttribute("src");
      if (!src) return null;
      return {
        src: src,
        alt: (img.getAttribute("alt") || "").trim(),
        node: img,
      };
    })
    .filter(Boolean);

  if (!images.length) return;

  const overlay = document.createElement("div");
  overlay.className = "post-lightbox";
  overlay.setAttribute("aria-hidden", "true");
  overlay.innerHTML =
    '<button type="button" class="post-lightbox-close" aria-label="Schliessen">×</button>' +
    '<button type="button" class="post-lightbox-prev" aria-label="Vorheriges Bild">←</button>' +
    '<figure class="post-lightbox-figure">' +
    '<img class="post-lightbox-image" alt="">' +
    '<figcaption class="post-lightbox-caption"></figcaption>' +
    "</figure>" +
    '<button type="button" class="post-lightbox-next" aria-label="Naechstes Bild">→</button>';
  document.body.appendChild(overlay);

  const imageEl = overlay.querySelector(".post-lightbox-image");
  const captionEl = overlay.querySelector(".post-lightbox-caption");
  const closeEl = overlay.querySelector(".post-lightbox-close");
  const prevEl = overlay.querySelector(".post-lightbox-prev");
  const nextEl = overlay.querySelector(".post-lightbox-next");

  let currentIndex = 0;
  let isOpen = false;

  const render = () => {
    const item = images[currentIndex];
    if (!item) return;
    imageEl.src = item.src;
    imageEl.alt = item.alt;
    captionEl.textContent = item.alt;
    captionEl.style.display = item.alt ? "block" : "none";
    const many = images.length > 1;
    prevEl.style.display = many ? "inline-flex" : "none";
    nextEl.style.display = many ? "inline-flex" : "none";
  };

  const openAt = (index) => {
    currentIndex = index;
    render();
    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden", "false");
    document.body.classList.add("post-lightbox-open");
    isOpen = true;
  };

  const close = () => {
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.classList.remove("post-lightbox-open");
    isOpen = false;
  };

  const step = (delta) => {
    if (!images.length) return;
    currentIndex = (currentIndex + delta + images.length) % images.length;
    render();
  };

  images.forEach((item, index) => {
    item.node.classList.add("has-lightbox");
    item.node.addEventListener("click", (event) => {
      event.preventDefault();
      openAt(index);
    });
    const anchor = item.node.closest("a");
    if (anchor) {
      anchor.addEventListener("click", (event) => {
        event.preventDefault();
        openAt(index);
      });
    }
  });

  closeEl.addEventListener("click", close);
  prevEl.addEventListener("click", () => step(-1));
  nextEl.addEventListener("click", () => step(1));
  overlay.addEventListener("click", (event) => {
    if (event.target === overlay) close();
  });

  document.addEventListener("keydown", (event) => {
    if (!isOpen) return;
    if (event.key === "Escape") close();
    if (event.key === "ArrowLeft") step(-1);
    if (event.key === "ArrowRight") step(1);
  });
})();
