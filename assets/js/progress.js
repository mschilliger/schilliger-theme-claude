(function () {
  const bar = document.getElementById("bar");
  if (!bar) return;

  const update = () => {
    const total = document.documentElement.scrollHeight - window.innerHeight;
    const value = total > 0 ? window.scrollY / total : 0;
    bar.style.transform = `scaleX(${Math.max(0, Math.min(1, value))})`;
  };

  document.addEventListener("scroll", update, { passive: true });
  update();
})();
