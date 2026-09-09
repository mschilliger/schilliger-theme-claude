(function () {
  if (typeof schilligerNewsletter === "undefined") return;

  const pageLoadedAt = Date.now();
  const MIN_SUBMIT_DELAY_MS = 2000;

  const isLikelyBot = (form) => {
    const honeypot = form.querySelector('input[name="hp"]');
    if (honeypot && honeypot.value) return true;
    return Date.now() - pageLoadedAt < MIN_SUBMIT_DELAY_MS;
  };

  const setFeedback = (container, message, isError) => {
    const feedback = container.querySelector(".nl-feedback");
    if (!feedback) return;
    feedback.textContent = message;
    feedback.classList.toggle("is-error", Boolean(isError));
    feedback.classList.toggle("is-success", !isError && Boolean(message));
  };

  const mailerliteForms = Array.from(document.querySelectorAll(".nl-form.is-mailerlite"));
  if (mailerliteForms.length) {
    let activeMailerliteForm = null;

    mailerliteForms.forEach((mailerliteForm) => {
      const container = mailerliteForm.closest(".nl-widget") || document;
      const submitButton = mailerliteForm.querySelector(".nl-btn");
      const rowForm = container.querySelector(".row-form");
      const rowSuccess = container.querySelector(".row-success");
      const targetName = mailerliteForm.getAttribute("target");
      const targetFrame = targetName ? document.querySelector("iframe[name='" + targetName + "']") : null;
      let submitted = false;
      let isSuccess = false;

      const showSuccess = () => {
        isSuccess = true;
        if (rowSuccess) rowSuccess.style.display = "block";
        if (rowForm) rowForm.style.display = "none";
        setFeedback(container, "Danke fuer deine Anmeldung!", false);
        mailerliteForm.reset();
        if (submitButton) submitButton.disabled = false;
      };

      mailerliteForm.addEventListener("submit", (event) => {
        if (isLikelyBot(mailerliteForm)) {
          event.preventDefault();
          showSuccess();
          return;
        }
        submitted = true;
        isSuccess = false;
        activeMailerliteForm = showSuccess;
        setFeedback(container, "", false);
        if (submitButton) submitButton.disabled = true;
      });

      if (targetFrame) {
        targetFrame.addEventListener("load", () => {
          if (submitted && !isSuccess) {
            showSuccess();
          }
        });
      }
    });

    window.ml_webform_success_38377567 = function () {
      if (typeof activeMailerliteForm === "function") {
        activeMailerliteForm();
      }
    };

    if (schilligerNewsletter.mailerliteTakelUrl) {
      fetch(schilligerNewsletter.mailerliteTakelUrl).catch(() => {});
    }

    return;
  }

  const fallbackForm = document.querySelector(".nl-form.is-ajax");
  if (!fallbackForm) return;
  const fallbackContainer = fallbackForm.closest(".nl-widget") || document;

  const submitButton = fallbackForm.querySelector(".nl-btn");

  fallbackForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    setFeedback(fallbackContainer, "", false);

    if (isLikelyBot(fallbackForm)) {
      setFeedback(fallbackContainer, "Danke! Die Anmeldung ist eingegangen.", false);
      fallbackForm.reset();
      return;
    }

    const emailInput = fallbackForm.querySelector("input[type='email']");
    const email = emailInput ? emailInput.value.trim() : "";
    if (!email) {
      setFeedback(fallbackContainer, "Bitte eine gueltige E-Mail-Adresse eingeben.", true);
      return;
    }

    submitButton.disabled = true;

    try {
      const body = new URLSearchParams();
      body.append("action", "schilliger_newsletter_signup");
      body.append("nonce", schilligerNewsletter.nonce);
      body.append("email", email);

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

      setFeedback(fallbackContainer, payload.data?.message || "Danke! Die Anmeldung ist eingegangen.", false);
      fallbackForm.reset();
    } catch (error) {
      setFeedback(fallbackContainer, error.message || "Die Anmeldung ist fehlgeschlagen.", true);
    } finally {
      submitButton.disabled = false;
    }
  });
})();
