document.addEventListener("DOMContentLoaded", function () {
  // ========== TOAST NOTIFICATION ==========
  const toast = document.getElementById("toast");

  function showToast(message, type = "success") {
    if (!toast) return;

    toast.textContent = message;
    toast.classList.remove("hide");
    toast.style.animation = "toastIn 0.5s forwards";

    setTimeout(() => {
      toast.style.animation = "toastOut 0.5s forwards";
      setTimeout(() => {
        toast.classList.add("hide");
      }, 500);
    }, 2500);
  }

  // ========== ADD TO CART ==========
  document.querySelectorAll(".addCart").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const id = this.dataset.id;
      const originalText = this.innerHTML;

      // Animasi button
      this.style.transform = "scale(0.95)";
      this.innerHTML = "...";

      fetch("cart/add_ajax.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "product_id=" + id + "&qty=1",
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Update cart count di navbar
            const cartLink = document.querySelector(
              'header nav a[href*="cart"]',
            );
            if (cartLink) {
              cartLink.innerHTML = `CART (${data.cartCount})`;
            }

            showToast("✓ ADDED TO CART");

            // Animasi button sukses
            this.style.background = "#22c55e";
            this.innerHTML = "✓ ADDED";
            setTimeout(() => {
              this.style.transform = "";
              this.style.background = "";
              this.innerHTML = originalText;
            }, 2000);
          } else {
            showToast("✗ " + data.message);
            this.style.transform = "";
            this.innerHTML = originalText;
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showToast("✗ ERROR");
          this.style.transform = "";
          this.innerHTML = originalText;
        });
    });
  });

  // ========== ADD TO WISHLIST ==========
  document.querySelectorAll(".addWishlist").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const id = this.dataset.id;

      fetch("wishlist_add.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id,
      })
        .then((response) => response.text())
        .then((count) => {
          // Update wishlist count
          const wishlistLink = document.querySelector(
            'header nav a[href*="wishlist"]',
          );
          if (wishlistLink) {
            wishlistLink.innerHTML = `WISHLIST (${count})`;
          }

          showToast("❤️ ADDED TO WISHLIST");

          // Animasi button
          this.style.background = "#ef4444";
          this.innerHTML = "❤️ ADDED";
          setTimeout(() => {
            this.style.background = "transparent";
            this.innerHTML = "♥ WISHLIST";
          }, 2000);
        });
    });
  });

  // ========== GSAP ANIMATIONS ==========
  if (typeof gsap !== "undefined") {
    gsap.registerPlugin(ScrollTrigger);

    // Hero animation
    gsap.from(".hero h1", {
      y: 200,
      opacity: 0,
      duration: 1.5,
      ease: "power4.out",
    });

    gsap.from(".hero p", {
      y: 50,
      opacity: 0,
      duration: 1,
      delay: 0.5,
      ease: "power3.out",
    });

    // Cards stagger
    gsap.from(".card", {
      y: 100,
      opacity: 0,
      duration: 1,
      stagger: 0.2,
      ease: "power3.out",
      scrollTrigger: {
        trigger: ".products",
        start: "top 80%",
      },
    });
  }

  // ========== SMOOTH SCROLL ==========
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: "smooth" });
      }
    });
  });

  // ========== HEADER SCROLL EFFECT ==========
  const header = document.querySelector("header");
  let lastScroll = 0;

  window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }

    if (currentScroll > lastScroll && currentScroll > 200) {
      header.style.transform = "translateY(-100%)";
    } else {
      header.style.transform = "translateY(0)";
    }

    lastScroll = currentScroll;
  });
});
