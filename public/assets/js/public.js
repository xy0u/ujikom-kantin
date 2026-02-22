/**
 * Kantin Digital — Public JS
 * ============================================================
 */
(function () {
  "use strict";

  /* ── DOM Ready ──────────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", init);

  function init() {
    initNavbar();
    initToasts();
    initLazyImages();
    initScrollReveal();
    initProductCards();
  }

  /* ── Navbar ─────────────────────────────────────────────────── */
  function initNavbar() {
    const navbar = document.getElementById("navbar");
    const toggle = document.getElementById("navToggle");
    const links = document.getElementById("navLinks");
    const userMenu = document.getElementById("userMenu");
    const userBtn = document.getElementById("userMenuBtn");
    const dropdown = document.getElementById("userDropdown");

    // Sticky + scroll class
    if (navbar) {
      let lastScroll = 0;
      window.addEventListener(
        "scroll",
        () => {
          const curr = window.scrollY;
          navbar.classList.toggle("navbar--scrolled", curr > 50);
          navbar.classList.toggle(
            "navbar--hidden",
            curr > lastScroll && curr > 200,
          );
          lastScroll = curr;
        },
        { passive: true },
      );
    }

    // Mobile hamburger
    if (toggle && links) {
      toggle.addEventListener("click", () => {
        const open = links.classList.toggle("open");
        toggle.classList.toggle("open", open);
        toggle.setAttribute("aria-expanded", open);
      });

      // Close on outside click
      document.addEventListener("click", (e) => {
        if (!toggle.contains(e.target) && !links.contains(e.target)) {
          links.classList.remove("open");
          toggle.classList.remove("open");
        }
      });
    }

    // User dropdown
    if (userBtn && dropdown) {
      userBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        const open = dropdown.classList.toggle("open");
        userBtn.setAttribute("aria-expanded", open);
      });
      document.addEventListener("click", (e) => {
        if (!userMenu?.contains(e.target)) {
          dropdown.classList.remove("open");
          userBtn?.setAttribute("aria-expanded", "false");
        }
      });
    }
  }

  /* ── Toasts ─────────────────────────────────────────────────── */
  function initToasts() {
    // Expose globally
    window.showToast = showToast;
  }

  function showToast(message, type = "info", duration = 4000) {
    let container = document.getElementById("toastContainer");
    if (!container) {
      container = document.createElement("div");
      container.id = "toastContainer";
      container.className = "toast-container";
      document.body.appendChild(container);
    }

    const icons = {
      success: '<path d="M20 6 9 17l-5-5"/>',
      error:
        '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
      warning:
        '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
      info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
    };

    const toast = document.createElement("div");
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `
            <svg class="toast__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${icons[type] || icons.info}
            </svg>
            <span class="toast__msg">${message}</span>
            <button class="toast__close" aria-label="Tutup">&times;</button>
        `;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add("show"));

    const close = () => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 300);
    };

    toast.querySelector(".toast__close").addEventListener("click", close);
    if (duration > 0) setTimeout(close, duration);

    return toast;
  }

  /* ── Lazy Images ────────────────────────────────────────────── */
  function initLazyImages() {
    if (!("IntersectionObserver" in window)) return;

    const imgs = document.querySelectorAll('img[loading="lazy"]');
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.classList.add("loaded");
            observer.unobserve(img);
          }
        });
      },
      { rootMargin: "100px" },
    );

    imgs.forEach((img) => observer.observe(img));
  }

  /* ── Scroll Reveal ──────────────────────────────────────────── */
  function initScrollReveal() {
    if (!("IntersectionObserver" in window)) return;

    const els = document.querySelectorAll(
      ".feature-card, .product-card, .section-header",
    );
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry, i) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.classList.add("revealed");
            }, i * 60);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 },
    );

    els.forEach((el) => {
      el.classList.add("reveal");
      observer.observe(el);
    });
  }

  /* ── Product Cards ──────────────────────────────────────────── */
  function initProductCards() {
    // Hover tilt effect (subtle)
    const cards = document.querySelectorAll(".product-card");
    cards.forEach((card) => {
      card.addEventListener("mousemove", handleTilt);
      card.addEventListener("mouseleave", resetTilt);
    });
  }

  function handleTilt(e) {
    const card = e.currentTarget;
    const rect = card.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    const tiltX = -y * 4;
    const tiltY = x * 4;
    card.style.transform = `perspective(800px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateY(-4px)`;
  }

  function resetTilt(e) {
    e.currentTarget.style.transform = "";
  }

  /* ── Cart Count Update ──────────────────────────────────────── */
  window.updateCartCount = function (count) {
    let badge = document.querySelector(".nav-cart-count");
    if (count > 0) {
      if (!badge) {
        badge = document.createElement("span");
        badge.className = "nav-cart-count";
        document.querySelector(".nav-cart-btn")?.appendChild(badge);
      }
      badge.textContent = count;
    } else if (badge) {
      badge.remove();
    }
  };

  /* ── Add to Cart ────────────────────────────────────────────── */
  window.addToCart = async function (id, name, price) {
    try {
      const res = await fetch("/public/cart/action.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "add", id, name, price, qty: 1 }),
      });
      const data = await res.json();
      if (data.success) {
        showToast(`${name} ditambahkan ke keranjang!`, "success");
        updateCartCount(data.cart_count);
      } else {
        showToast(data.message || "Gagal menambahkan produk", "error");
      }
    } catch (err) {
      showToast("Terjadi kesalahan koneksi", "error");
    }
  };

  /* ── Wishlist Toggle ────────────────────────────────────────── */
  window.toggleWishlist = async function (id, btn) {
    try {
      const res = await fetch("/public/wishlist.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "toggle", id }),
      });
      const data = await res.json();
      if (data.success) {
        const active = data.added;
        btn.classList.toggle("active", active);
        const path = btn.querySelector("path");
        if (path) path.setAttribute("fill", active ? "currentColor" : "none");
        showToast(
          active ? "Ditambahkan ke wishlist" : "Dihapus dari wishlist",
          "success",
        );
      }
    } catch {
      showToast("Terjadi kesalahan", "error");
    }
  };

  /* ── Modal ──────────────────────────────────────────────────── */
  window.openModal = function (id) {
    const m = document.getElementById(id);
    if (m) {
      m.classList.add("active");
      document.body.style.overflow = "hidden";
    }
  };
  window.closeModal = function (id) {
    const m = document.getElementById(id);
    if (m) {
      m.classList.remove("active");
      document.body.style.overflow = "";
    }
  };
  // Close on backdrop click
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal-overlay")) {
      e.target.classList.remove("active");
      document.body.style.overflow = "";
    }
  });
  // Close on Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll(".modal-overlay.active").forEach((m) => {
        m.classList.remove("active");
        document.body.style.overflow = "";
      });
    }
  });

  /* ── Smooth Scroll ──────────────────────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener("click", (e) => {
      const target = document.querySelector(a.getAttribute("href"));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });
})();
