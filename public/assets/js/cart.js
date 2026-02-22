// ============================================
// KARTIN DIGITAL - CART.JS UPGRADED VERSION
// ============================================

class CartManager {
  constructor() {
    this.cart = {};
    this.init();
  }

  init() {
    this.loadCart();
    this.setupEventListeners();
  }

  loadCart() {
    // Cart sudah di session, kita hanya perlu membaca dari server
    // Tapi kita bisa menyimpan state di client untuk UI yang responsif
    this.updateCartDisplay();
  }

  async addToCart(productId, variantId = 0, qty = 1) {
    const formData = new FormData();
    formData.append("product_id", productId);
    formData.append("variant_id", variantId);
    formData.append("qty", qty);

    try {
      const response = await fetch("add_ajax.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        this.updateCartCount(data.cartCount);
        this.showNotification("✓ Produk ditambahkan ke keranjang", "success");
        return true;
      } else {
        this.showNotification("✗ " + data.message, "error");
        return false;
      }
    } catch (error) {
      console.error("Cart Error:", error);
      this.showNotification("✗ Gagal menambahkan ke keranjang", "error");
      return false;
    }
  }

  async removeFromCart(key) {
    try {
      const response = await fetch(`remove.php?key=${encodeURIComponent(key)}`);
      if (response.ok) {
        this.showNotification("✓ Produk dihapus dari keranjang", "success");
        return true;
      }
    } catch (error) {
      console.error("Remove Error:", error);
      this.showNotification("✗ Gagal menghapus produk", "error");
      return false;
    }
  }

  updateCartCount(count) {
    const cartLink = document.querySelector('header nav a[href*="cart"]');
    if (cartLink) {
      let badge = cartLink.querySelector(".cart-count");
      if (!badge) {
        badge = document.createElement("span");
        badge.className = "cart-count";
        cartLink.classList.add("cart-link");
        cartLink.appendChild(badge);
      }
      badge.textContent = count;

      // Animasi
      badge.style.transform = "scale(1.5)";
      setTimeout(() => {
        badge.style.transform = "scale(1)";
      }, 200);
    }
  }

  updateCartDisplay() {
    // Update UI keranjang jika diperlukan
    const cartItems = document.querySelectorAll(".cart-item");
    // Logic update display
  }

  showNotification(message, type = "success") {
    if (typeof window.showToast === "function") {
      window.showToast(message, type);
    } else {
      alert(message);
    }
  }

  setupEventListeners() {
    // Tombol add to cart
    document.querySelectorAll(".add-to-cart").forEach((btn) => {
      btn.addEventListener("click", async (e) => {
        e.preventDefault();
        const id = btn.dataset.id;
        await this.addToCart(id);
      });
    });

    // Tombol remove dari cart
    document.querySelectorAll(".remove-from-cart").forEach((btn) => {
      btn.addEventListener("click", async (e) => {
        e.preventDefault();
        const key = btn.dataset.key;
        if (confirm("Hapus item dari keranjang?")) {
          await this.removeFromCart(key);
          btn.closest(".cart-item").remove();
        }
      });
    });
  }
}

// Initialize cart manager
const cartManager = new CartManager();
