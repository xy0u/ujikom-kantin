document.addEventListener("DOMContentLoaded", function () {
  const authBox = document.querySelector(".auth-box");
  const inputs = document.querySelectorAll("input");
  const btn = document.querySelector(".btn-auth");

  // 1. Efek Fade In saat halaman dimuat
  authBox.style.opacity = "0";
  authBox.style.transform = "translateY(20px)";
  setTimeout(() => {
    authBox.style.transition = "all 0.6s ease-out";
    authBox.style.opacity = "1";
    authBox.style.transform = "translateY(0)";
  }, 100);

  // 2. Animasi Fokus Input
  inputs.forEach((input) => {
    input.addEventListener("focus", () => {
      input.parentElement.style.transform = "scale(1.02)";
      input.parentElement.style.transition = "0.3s";
    });
    input.addEventListener("blur", () => {
      input.parentElement.style.transform = "scale(1)";
    });
  });

  // 3. Loading State pada Tombol
  if (btn) {
    btn.addEventListener("click", function () {
      // Cek apakah input kosong (validasi dasar HTML5 tetap jalan)
      let isEmpty = false;
      inputs.forEach((i) => {
        if (i.value === "") isEmpty = true;
      });

      if (!isEmpty) {
        btn.innerHTML = "Memproses...";
        btn.style.opacity = "0.7";
        btn.style.cursor = "not-allowed";
      }
    });
  }
});
