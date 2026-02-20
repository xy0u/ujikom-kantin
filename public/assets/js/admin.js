document.addEventListener("DOMContentLoaded", function () {
  // 1. Highlight Menu Aktif secara otomatis
  const currentPath = window.location.pathname.split("/").pop();
  const navLinks = document.querySelectorAll(".sidebar nav a");

  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentPath) {
      link.classList.add("active");
    }
  });

  // 2. Efek Fade-in untuk main content
  const mainContent = document.querySelector(".main-content");
  mainContent.style.opacity = "0";
  mainContent.style.transition = "opacity 0.5s ease";
  setTimeout(() => (mainContent.style.opacity = "1"), 50);

  // 3. Konfirmasi Hapus yang lebih clean
  window.hapusData = function (url) {
    if (confirm("Data akan dihapus permanen. Lanjutkan?")) {
      window.location.href = url;
    }
  };
});
