/* ============================================
   ELVA BRUTALIST SYSTEM - KANTIN
   public.js
   ============================================ */

document.addEventListener("DOMContentLoaded", () => {
  /* ---- TOAST NOTIFICATION ---- */
  const toast = document.createElement("div");
  toast.className = "toast";
  toast.textContent = "ADDED TO CART";
  document.body.appendChild(toast);

  let toastTimer;
  function showToast(message) {
    toast.textContent = message || "ADDED TO CART";
    toast.classList.add("show");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove("show"), 2200);
  }

  /* ---- ADD TO CART (AJAX) ---- */
  document.querySelectorAll(".addCart").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      const id = btn.dataset.id;

      // Ripple effect on button
      btn.style.transform = "scale(0.95)";
      setTimeout(() => (btn.style.transform = ""), 200);

      fetch("cart/add.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id,
      })
        .then((r) => r.text())
        .then(() => {
          showToast("ADDED TO CART");
          // Update cart count in nav
          const cartLink = document.querySelector('header nav a[href*="cart"]');
          if (cartLink) {
            const match = cartLink.textContent.match(/\d+/);
            const current = match ? parseInt(match[0]) : 0;
            cartLink.textContent = "Cart (" + (current + 1) + ")";
          }
        })
        .catch(() => showToast("ERROR - TRY AGAIN"));
    });
  });

  /* ---- GSAP CARD ANIMATIONS ---- */
  if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
    gsap.registerPlugin(ScrollTrigger);

    // Hero title reveal
    const heroTitle = document.querySelector(".hero h1");
    if (heroTitle) {
      gsap.fromTo(
        heroTitle,
        { y: 120, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 1.4,
          ease: "power4.out",
          delay: 0.2,
        },
      );
    }

    // Hero subtitle
    const heroP = document.querySelector(".hero p");
    if (heroP) {
      gsap.fromTo(
        heroP,
        { y: 40, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 1,
          ease: "power3.out",
          delay: 0.6,
        },
      );
    }

    // Product cards stagger in
    const cards = document.querySelectorAll(".card");
    cards.forEach((card, i) => {
      gsap.fromTo(
        card,
        { y: 100, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 1,
          ease: "power3.out",
          scrollTrigger: {
            trigger: card,
            start: "top 90%",
            end: "top 50%",
            toggleActions: "play none none none",
          },
          delay: (i % 2) * 0.15, // stagger left/right columns
        },
      );
    });

    // Header fade on scroll
    const header = document.querySelector("header");
    if (header) {
      let lastScroll = 0;
      window.addEventListener("scroll", () => {
        const current = window.scrollY;
        if (current > lastScroll && current > 200) {
          header.style.opacity = "0";
          header.style.transform = "translateY(-20px)";
          header.style.transition = "all 0.5s cubic-bezier(0.8, 0, 0.2, 1)";
        } else {
          header.style.opacity = "1";
          header.style.transform = "translateY(0)";
        }
        lastScroll = current;
      });
    }
  }

  /* ---- SMOOTH SCROLL FOR ANCHOR LINKS ---- */
  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener("click", (e) => {
      e.preventDefault();
      const target = document.querySelector(a.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });
});
