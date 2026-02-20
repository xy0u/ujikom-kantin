// Cart functionality with AJAX
function addToCart(productId, variantId = 0, qty = 1) {
  const button = event.target;
  const originalText = button.innerHTML;

  // Disable button and show loading
  button.disabled = true;
  button.innerHTML = "...";

  const formData = new FormData();
  formData.append("product_id", productId);
  formData.append("variant_id", variantId);
  formData.append("qty", qty);

  fetch("cart/add_ajax.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update cart badge
        const badge = document.getElementById("cart-badge");
        if (badge) {
          badge.innerText = data.cartCount;
          badge.style.transform = "scale(1.2)";
          setTimeout(() => {
            badge.style.transform = "scale(1)";
          }, 200);
        }

        // Show success message
        showNotification("✓ " + data.message, "success");

        // Animate button
        button.style.background = "#22c55e";
        button.innerHTML = "✓ Added";
        setTimeout(() => {
          button.innerHTML = originalText;
          button.style.background = "";
        }, 2000);
      } else {
        showNotification("✗ " + data.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("✗ Error adding to cart", "error");
    })
    .finally(() => {
      button.disabled = false;
    });
}

// Show notification
function showNotification(message, type = "success") {
  // Remove existing notification
  const existing = document.querySelector(".cart-notification");
  if (existing) {
    existing.remove();
  }

  // Create notification
  const notification = document.createElement("div");
  notification.className = "cart-notification " + type;
  notification.innerHTML = message;
  notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === "success" ? "#22c55e" : "#ef4444"};
        color: white;
        padding: 12px 24px;
        border-radius: 0;
        font-weight: 600;
        letter-spacing: 1px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        border: 2px solid black;
    `;

  document.body.appendChild(notification);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease";
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

// Add animation styles
const style = document.createElement("style");
style.innerHTML = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
