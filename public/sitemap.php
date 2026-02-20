<?php
session_start();
require "core/helpers.php";

$cartCount = getCartCount();
include "components/navbar.php";
?>

<main>
     <section class="hero" style="height: 30vh;">
          <h1>SITEMAP</h1>
          <p>Navigasi Lengkap Kantin Digital</p>
     </section>

     <div style="max-width: 1200px; margin: 0 auto; padding: 60px 20px;">
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
               <!-- Public Pages -->
               <div style="background: var(--surface); border: 3px solid var(--fg); padding: 30px;">
                    <h2 style="margin-bottom: 20px;">PUBLIC</h2>
                    <ul style="list-style: none;">
                         <li style="margin-bottom: 10px;"><a href="public/index.php">🏠 Home</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/index.php#menu">🍽️ Menu</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/about.php">📖 About</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/contact.php">📞 Contact</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/faq.php">❓ FAQ</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/search.php">🔍 Search</a></li>
                    </ul>
               </div>

               <!-- User Pages -->
               <div style="background: var(--surface); border: 3px solid var(--fg); padding: 30px;">
                    <h2 style="margin-bottom: 20px;">USER</h2>
                    <ul style="list-style: none;">
                         <li style="margin-bottom: 10px;"><a href="public/profile.php">👤 Profile</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/orders.php">📦 Orders</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/wishlist.php">❤️ Wishlist</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/cart/index.php">🛒 Cart</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/checkout/index.php">💳 Checkout</a></li>
                    </ul>
               </div>

               <!-- Auth & Legal -->
               <div style="background: var(--surface); border: 3px solid var(--fg); padding: 30px;">
                    <h2 style="margin-bottom: 20px;">ACCOUNT</h2>
                    <ul style="list-style: none;">
                         <li style="margin-bottom: 10px;"><a href="auth/login.php">🔑 Login</a></li>
                         <li style="margin-bottom: 10px;"><a href="auth/register.php">📝 Register</a></li>
                         <li style="margin-bottom: 10px;"><a href="auth/logout.php">🚪 Logout</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/terms.php">📜 Terms</a></li>
                         <li style="margin-bottom: 10px;"><a href="public/privacy.php">🔒 Privacy</a></li>
                    </ul>
               </div>
          </div>

          <!-- Admin Link (if admin) -->
          <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
               <div style="margin-top: 40px; background: var(--surface); border: 3px solid #ef4444; padding: 30px;">
                    <h2 style="color: #ef4444; margin-bottom: 20px;">ADMIN PANEL</h2>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                         <a href="admin/dashboard.php">📊 Dashboard</a>
                         <a href="admin/products.php">📦 Products</a>
                         <a href="admin/categories.php">📁 Categories</a>
                         <a href="admin/orders.php">📋 Orders</a>
                    </div>
               </div>
          <?php endif; ?>
     </div>
</main>

<?php include "components/footer.php"; ?>