(function () {
  const mq = window.matchMedia("(pointer: fine)");
  const cursor = document.getElementById("cur");
  if (!cursor || !mq.matches) return;

  document.body.classList.add("r");

  document.addEventListener("mousemove", (e) => {
    cursor.style.left = `${e.clientX}px`;
    cursor.style.top = `${e.clientY}px`;
  });

  document.addEventListener("mousedown", () => document.body.classList.add("d"));
  document.addEventListener("mouseup", () => document.body.classList.remove("d"));
})();
