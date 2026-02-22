/**
 * Kantin Digital — Admin JS
 * ============================================================
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", init);

  function init() {
    initSidebar();
    initModals();
    initTableSearch();
    initAlerts();
    initConfirmDeletes();
  }

  /* ── Sidebar ─────────────────────────────────────────────────── */
  function initSidebar() {
    const toggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("adminSidebar");
    const main = document.getElementById("adminMain");

    if (!toggle || !sidebar) return;

    const COLLAPSED_KEY = "admin_sidebar_collapsed";
    const isCollapsed = localStorage.getItem(COLLAPSED_KEY) === "true";
    if (isCollapsed) {
      sidebar.classList.add("collapsed");
      main?.classList.add("sidebar-collapsed");
    }

    toggle.addEventListener("click", () => {
      const collapsed = sidebar.classList.toggle("collapsed");
      main?.classList.toggle("sidebar-collapsed", collapsed);
      toggle.classList.toggle("open", !collapsed);
      localStorage.setItem(COLLAPSED_KEY, collapsed);
    });

    // Mobile overlay close
    document.addEventListener("click", (e) => {
      if (window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
          sidebar.classList.remove("open");
        }
      }
    });

    // Mobile open
    toggle.addEventListener("click", () => {
      if (window.innerWidth <= 768) {
        sidebar.classList.toggle("open");
      }
    });
  }

  /* ── Modals ──────────────────────────────────────────────────── */
  function initModals() {
    window.openModal = function (id) {
      const m = document.getElementById(id);
      if (m) {
        m.classList.add("active");
        document.body.style.overflow = "hidden";
        const firstInput = m.querySelector(
          "input:not([type=hidden]), textarea, select",
        );
        firstInput?.focus();
      }
    };

    window.closeModal = function (id) {
      const m = document.getElementById(id);
      if (m) {
        m.classList.remove("active");
        document.body.style.overflow = "";
      }
    };

    // Close on backdrop
    document.querySelectorAll(".modal-overlay").forEach((overlay) => {
      overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
          overlay.classList.remove("active");
          document.body.style.overflow = "";
        }
      });
    });

    // Escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        document.querySelectorAll(".modal-overlay.active").forEach((m) => {
          m.classList.remove("active");
          document.body.style.overflow = "";
        });
      }
    });
  }

  /* ── Table Search ────────────────────────────────────────────── */
  function initTableSearch() {
    const searchInputs = document.querySelectorAll("[data-table-search]");
    searchInputs.forEach((input) => {
      const targetId = input.getAttribute("data-table-search");
      const table = document.getElementById(targetId);
      if (!table) return;
      const rows = table.querySelectorAll("tbody tr");

      input.addEventListener("input", () => {
        const q = input.value.toLowerCase().trim();
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(q) ? "" : "none";
        });
      });
    });
  }

  /* ── Auto-dismiss Alerts ─────────────────────────────────────── */
  function initAlerts() {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
      const closeBtn = document.createElement("button");
      closeBtn.innerHTML = "&times;";
      closeBtn.className = "alert__close";
      closeBtn.addEventListener("click", () => {
        alert.style.opacity = "0";
        setTimeout(() => alert.remove(), 300);
      });
      alert.appendChild(closeBtn);

      // Auto dismiss success after 4s
      if (alert.classList.contains("alert--success")) {
        setTimeout(() => {
          alert.style.opacity = "0";
          setTimeout(() => alert.remove(), 300);
        }, 4000);
      }
    });
  }

  /* ── Confirm Deletes ─────────────────────────────────────────── */
  function initConfirmDeletes() {
    document.querySelectorAll("[data-confirm]").forEach((el) => {
      el.addEventListener("click", (e) => {
        const msg =
          el.getAttribute("data-confirm") || "Yakin ingin melanjutkan?";
        if (!confirm(msg)) e.preventDefault();
      });
    });
  }

  /* ── Image Preview ───────────────────────────────────────────── */
  window.previewImage = function (input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);
  };

  /* ── Toast ───────────────────────────────────────────────────── */
  window.showToast = function (message, type = "info") {
    let container = document.getElementById("toastContainer");
    if (!container) {
      container = document.createElement("div");
      container.id = "toastContainer";
      container.className = "toast-container";
      document.body.appendChild(container);
    }
    const toast = document.createElement("div");
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()">&times;</button>`;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add("show"), 10);
    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  };

  /* ── Responsive table horizontal scroll hint ──────────────────── */
  function initTableHints() {
    const tables = document.querySelectorAll(".table-wrap");
    tables.forEach((wrap) => {
      if (wrap.scrollWidth > wrap.clientWidth) {
        wrap.classList.add("scrollable");
      }
    });
  }
})();
