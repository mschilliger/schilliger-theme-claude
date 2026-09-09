(function () {
  if (typeof schilligerNewsletter === "undefined") return;

  const setFeedback = (container, message, isError) => {
    const feedback = container.querySelector(".nl-feedback");
    if (!feedback) return;
    feedback.textContent = message;
    feedback.classList.toggle("is-error", Boolean(isError));
    feedback.classList.toggle("is-success", !isError && Boolean(message));
  };

  const showSuccess = (form, container) => {
    const rowForm = container.querySelector(".row-form");
    const rowSuccess = container.querySelector(".row-success");
    if (rowSuccess) rowSuccess.style.display = "block";
    if (rowForm) rowForm.style.display = "none";
    setFeedback(container, "Danke fuer deine Anmeldung!", false);
    form.reset();
  };

  const forms = Array.from(document.querySelectorAll(".nl-form.is-ajax"));

  forms.forEach((form) => {
    const container = form.closest(".nl-widget") || document;
    const submitButton = form.querySelector(".nl-btn");
    const pageLoadedAt = Date.now();

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      setFeedback(container, "", false);

      const emailInput = form.querySelector("input[type='email']");
      const email = emailInput ? emailInput.value.trim() : "";
      if (!email) {
        setFeedback(container, "Bitte eine gueltige E-Mail-Adresse eingeben.", true);
        return;
      }

      const honeypot = form.querySelector('input[name="hp"]');
      const isLikelyBot = (honeypot && honeypot.value) || Date.now() - pageLoadedAt < 2000;
      if (isLikelyBot) {
        showSuccess(form, container);
        return;
      }

      if (submitButton) submitButton.disabled = true;

      try {
        const tsInput = form.querySelector('input[name="ts"]');
        const body = new URLSearchParams();
        body.append("action", "schilliger_newsletter_signup");
        body.append("nonce", schilligerNewsletter.nonce);
        body.append("email", email);
        body.append("hp", honeypot ? honeypot.value : "");
        body.append("ts", tsInput ? tsInput.value : "0");

        const response = await fetch(schilligerNewsletter.ajaxUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
          },
          body: body.toString(),
        });

        const payload = await response.json();
        if (!response.ok || !payload.success) {
          throw new Error(payload?.data?.message || "Die Anmeldung ist fehlgeschlagen.");
        }

        showSuccess(form, container);
      } catch (error) {
        setFeedback(container, error.message || "Die Anmeldung ist fehlgeschlagen.", true);
      } finally {
        if (submitButton) submitButton.disabled = false;
      }
    });
  });
})();
