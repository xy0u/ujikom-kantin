document.addEventListener("DOMContentLoaded", function () {
  /* ===== ACTIVE MENU ===== */
  const links = document.querySelectorAll(".sidebar nav a");

  links.forEach((link) => {
    if (link.href === window.location.href) {
      link.classList.add("active-link");
    }
  });

  /* ===== CONFIRM DELETE ===== */
  document.querySelectorAll(".btn-danger").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      if (!confirm("Yakin ingin menghapus data ini?")) {
        e.preventDefault();
      }
    });
  });
});
