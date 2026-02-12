document.addEventListener("DOMContentLoaded", function () {
  /* ================= NAVBAR SCROLL ================= */
  const navbar = document.querySelector(".navbar");
  if (navbar) {
    window.addEventListener("scroll", () => {
      navbar.classList.toggle("scrolled", window.scrollY > 50);
    });
  }

  /* ================= HAMBURGER ================= */
  const hamburger = document.querySelector(".hamburger");
  const nav = document.querySelector(".navbar nav");

  if (hamburger && nav) {
    hamburger.addEventListener("click", () => {
      nav.classList.toggle("show");
    });
  }

  /* ================= ADD TO CART ================= */
  document.querySelectorAll(".addCart").forEach((btn) => {
    btn.addEventListener("click", function () {
      if (btn.disabled) return;
      btn.disabled = true;

      const id = this.dataset.id;
      const qtyInput = document.querySelector(".qty[data-id='" + id + "']");
      const variantSelect = document.querySelector(
        ".variant-select[data-id='" + id + "']",
      );

      if (!qtyInput) {
        btn.disabled = false;
        return;
      }

      let qty = parseInt(qtyInput.value);
      if (isNaN(qty) || qty <= 0) qty = 1;

      let variant_id = variantSelect ? variantSelect.value : 0;

      fetch("cart/add_ajax.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_id=${id}&qty=${qty}&variant_id=${variant_id}`,
      })
        .then((res) => res.text())
        .then((count) => {
          if (!count || parseInt(count) === 0) {
            showToast("Failed to add");
            btn.disabled = false;
            return;
          }

          const cartCount = document.getElementById("cartCount");
          if (cartCount) cartCount.innerText = count;

          const card = btn.closest(".card");
          if (!card) {
            btn.disabled = false;
            return;
          }

          const stockBadge = card.querySelector(".stock-badge");

          let maxStock = parseInt(qtyInput.max);
          if (!isNaN(maxStock)) {
            maxStock -= qty;
            qtyInput.max = maxStock;

            if (maxStock <= 0 && stockBadge) {
              stockBadge.className = "stock-badge out";
              stockBadge.innerText = "Out of Stock";
              btn.innerText = "Out of Stock";
              btn.disabled = true;
              return;
            }
          }

          btn.disabled = false;
          showToast("Added to cart");
        })
        .catch(() => {
          showToast("Server error");
          btn.disabled = false;
        });
    });
  });

  /* ================= SMOOTH ANCHOR ================= */
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: "smooth" });
      }
    });
  });

  /* ================= PAGE TRANSITION ================= */
  document.querySelectorAll("a").forEach((link) => {
    if (
      link.href &&
      link.href.includes(window.location.origin) &&
      !link.href.includes("#") &&
      !link.hasAttribute("target")
    ) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        document.body.classList.add("fade-out");
        setTimeout(() => (window.location = this.href), 250);
      });
    }
  });

  /* ================= SCROLL REVEAL ================= */
  const reveals = document.querySelectorAll(".reveal");

  if (reveals.length > 0) {
    window.addEventListener("scroll", () => {
      reveals.forEach((el) => {
        if (el.getBoundingClientRect().top < window.innerHeight - 100) {
          el.classList.add("active");
        }
      });
    });
  }

  /* ================= CARD STAGGER ================= */
  const cards = document.querySelectorAll(".card");

  if (cards.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("show");
          }
        });
      },
      { threshold: 0.2 },
    );

    cards.forEach((card) => observer.observe(card));
  }

  /* ================= COUNTER ================= */
  const counterEl = document.getElementById("counter");

  if (counterEl) {
    let count = 0;
    const interval = setInterval(() => {
      count += 5;
      counterEl.innerText = count + "+ Happy Customers";
      if (count >= 500) clearInterval(interval);
    }, 20);
  }

  /* ================= WISHLIST ================= */
  document.querySelectorAll(".wishlist").forEach((btn) => {
    btn.addEventListener("click", function () {
      fetch("wishlist.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + this.dataset.id,
      })
        .then(() => showToast("Added to wishlist"))
        .catch(() => showToast("Error"));
    });
  });
});

/* ================= TOAST ================= */
function showToast(message) {
  let toast = document.createElement("div");
  toast.className = "toast";
  toast.innerText = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add("show"), 100);
  setTimeout(() => toast.remove(), 2000);
}
