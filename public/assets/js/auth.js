document.addEventListener("DOMContentLoaded", function () {
  const authBox = document.querySelector(".auth-box");
  const inputs = document.querySelectorAll("input");
  const btn = document.querySelector(".btn-auth");

  // 1. Efek Fade In
  if (authBox) {
    authBox.style.opacity = "0";
    authBox.style.transform = "translateY(20px)";
    setTimeout(() => {
      authBox.style.transition = "all 0.6s cubic-bezier(0.16, 1, 0.3, 1)";
      authBox.style.opacity = "1";
      authBox.style.transform = "translateY(0)";
    }, 100);
  }

  // 2. Animasi Fokus Input
  inputs.forEach((input) => {
    // Add floating label effect
    const formGroup = input.closest(".form-group");
    if (formGroup) {
      const label = formGroup.querySelector("label");
      if (label && input.value) {
        label.style.transform = "translateY(-20px) scale(0.8)";
        label.style.opacity = "0.8";
      }

      input.addEventListener("focus", () => {
        if (label) {
          label.style.transform = "translateY(-20px) scale(0.8)";
          label.style.opacity = "1";
          label.style.color = "var(--primary)";
        }
        formGroup.style.transform = "scale(1.02)";
        formGroup.style.transition = "0.3s cubic-bezier(0.16, 1, 0.3, 1)";
      });

      input.addEventListener("blur", () => {
        if (label && !input.value) {
          label.style.transform = "translateY(0) scale(1)";
          label.style.opacity = "0.6";
        }
        label.style.color = "";
        formGroup.style.transform = "scale(1)";
      });
    }

    // Add validation styling
    input.addEventListener("invalid", (e) => {
      e.preventDefault();
      input.style.borderColor = "#ef4444";
      input.style.boxShadow = "0 0 0 2px rgba(239, 68, 68, 0.2)";
    });

    input.addEventListener("input", () => {
      input.style.borderColor = "";
      input.style.boxShadow = "";
    });
  });

  // 3. Password strength indicator (untuk register)
  const passwordInput = document.querySelector('input[name="password"]');
  if (passwordInput && window.location.pathname.includes("register")) {
    const strengthIndicator = document.createElement("div");
    strengthIndicator.className = "password-strength";
    strengthIndicator.style.cssText = `
            height: 4px;
            background: #e2e8f0;
            margin-top: 8px;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.3s;
        `;

    const strengthBar = document.createElement("div");
    strengthBar.style.cssText = `
            height: 100%;
            width: 0;
            transition: all 0.3s;
        `;

    strengthIndicator.appendChild(strengthBar);
    passwordInput.parentElement.appendChild(strengthIndicator);

    passwordInput.addEventListener("input", function () {
      const password = this.value;
      let strength = 0;

      if (password.length >= 6) strength += 25;
      if (password.match(/[a-z]/)) strength += 25;
      if (password.match(/[A-Z]/)) strength += 25;
      if (password.match(/[0-9!@#$%^&*]/)) strength += 25;

      strengthBar.style.width = strength + "%";

      if (strength < 50) {
        strengthBar.style.background = "#ef4444";
      } else if (strength < 75) {
        strengthBar.style.background = "#eab308";
      } else {
        strengthBar.style.background = "#22c55e";
      }
    });
  }

  // 4. Loading State pada Tombol
  if (btn) {
    btn.addEventListener("click", function (e) {
      // Validasi form
      let isValid = true;
      inputs.forEach((i) => {
        if (i.hasAttribute("required") && !i.value) {
          isValid = false;
          i.style.borderColor = "#ef4444";
        }
      });

      if (isValid) {
        const originalText = btn.innerHTML;
        btn.innerHTML = "Processing...";
        btn.style.opacity = "0.7";
        btn.style.cursor = "not-allowed";

        // Prevent double submit
        setTimeout(() => {
          btn.innerHTML = originalText;
          btn.style.opacity = "1";
          btn.style.cursor = "pointer";
        }, 5000);
      }
    });
  }

  // 5. Toggle password visibility
  inputs.forEach((input) => {
    if (input.type === "password") {
      const toggleBtn = document.createElement("button");
      toggleBtn.type = "button";
      toggleBtn.innerHTML = "👁️";
      toggleBtn.style.cssText = `
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                font-size: 16px;
                opacity: 0.5;
                transition: opacity 0.3s;
            `;

      toggleBtn.addEventListener("mouseenter", () => {
        toggleBtn.style.opacity = "1";
      });

      toggleBtn.addEventListener("mouseleave", () => {
        toggleBtn.style.opacity = "0.5";
      });

      toggleBtn.addEventListener("click", () => {
        if (input.type === "password") {
          input.type = "text";
          toggleBtn.innerHTML = "🔒";
        } else {
          input.type = "password";
          toggleBtn.innerHTML = "👁️";
        }
      });

      input.parentElement.style.position = "relative";
      input.parentElement.appendChild(toggleBtn);
    }
  });

  // 6. Remember me functionality
  const rememberCheckbox = document.querySelector('input[name="remember"]');
  if (rememberCheckbox) {
    // Check if email was saved
    const savedEmail = localStorage.getItem("rememberedEmail");
    if (savedEmail) {
      const emailInput = document.querySelector('input[name="email"]');
      if (emailInput) {
        emailInput.value = savedEmail;
        rememberCheckbox.checked = true;
      }
    }

    rememberCheckbox.addEventListener("change", function () {
      const emailInput = document.querySelector('input[name="email"]');
      if (this.checked && emailInput) {
        localStorage.setItem("rememberedEmail", emailInput.value);
      } else {
        localStorage.removeItem("rememberedEmail");
      }
    });
  }
});
