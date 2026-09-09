(() => {
  const btn = document.querySelector("[data-schilliger-cover-fetch]");
  const statusEl = document.querySelector("[data-schilliger-cover-status]");
  if (!btn || !statusEl) return;

  const postIdInput = document.querySelector("#post_ID");
  const postId = postIdInput ? Number(postIdInput.value) : 0;
  if (!postId) return;

  const setStatus = (type, msg) => {
    statusEl.innerHTML = "";
    const p = document.createElement("p");
    p.style.margin = "0";
    p.textContent = msg;
    p.style.color = type === "error" ? "#b32d2e" : "#1d2327";
    statusEl.appendChild(p);
  };

  btn.addEventListener("click", async () => {
    if (!window.schilligerBookCover?.ajaxUrl || !window.schilligerBookCover?.nonce) {
      setStatus("error", "AJAX nicht verfügbar.");
      return;
    }

    btn.disabled = true;
    setStatus("ok", "Suche Cover …");

    try {
      const fd = new FormData();
      fd.append("action", "schilliger_book_cover_fetch");
      fd.append("nonce", window.schilligerBookCover.nonce);
      fd.append("postId", String(postId));

      const res = await fetch(window.schilligerBookCover.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        body: fd,
      });

      const json = await res.json().catch(() => null);
      if (!res.ok || !json || json.success !== true) {
        const msg = json?.data?.message || "Fehler beim Cover-Fetch.";
        setStatus("error", msg);
        btn.disabled = false;
        return;
      }

      setStatus("ok", json.data?.message || "Cover gesetzt. Bitte Seite kurz neu laden.");
      // Force-refresh the featured image metabox preview
      setTimeout(() => window.location.reload(), 700);
    } catch (e) {
      setStatus("error", "Netzwerkfehler beim Cover-Fetch.");
      btn.disabled = false;
    }
  });
})();

