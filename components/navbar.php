<?php
if (session_status() === PHP_SESSION_NONE)
     session_start();
$cartCount = getCartCount();
$currentPath = $_SERVER['REQUEST_URI'];

function isActive(string $path): string
{
     global $currentPath;
     return str_contains($currentPath, $path) ? 'active' : '';
}
?>
<nav class="navbar" id="navbar">
     <div class="nav-container">
          <!-- Logo -->
          <a href="/public/index.php" class="nav-logo">
               <span class="nav-logo-bracket">[</span>
               KD
               <span class="nav-logo-bracket">]</span>
          </a>

          <!-- Desktop Nav Links -->
          <ul class="nav-links" id="navLinks">
               <li><a href="/public/index.php" class="nav-link <?= isActive('/public/index.php') ?>">Menu</a></li>
               <li><a href="/public/about.php" class="nav-link <?= isActive('/about') ?>">About</a></li>
               <li><a href="/public/contact.php" class="nav-link <?= isActive('/contact') ?>">Contact</a></li>
          </ul>

          <!-- Nav Actions -->
          <div class="nav-actions">
               <!-- Search -->
               <a href="/public/search.php" class="nav-icon-btn" aria-label="Cari produk">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                         <circle cx="11" cy="11" r="8" />
                         <path d="m21 21-4.35-4.35" />
                    </svg>
               </a>

               <!-- Wishlist -->
               <?php if (isLoggedIn()): ?>
                    <a href="/public/wishlist.php" class="nav-icon-btn" aria-label="Wishlist">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path
                                   d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                         </svg>
                    </a>
               <?php endif; ?>

               <!-- Cart -->
               <a href="/public/cart/index.php" class="nav-cart-btn" aria-label="Keranjang">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                         <circle cx="9" cy="21" r="1" />
                         <circle cx="20" cy="21" r="1" />
                         <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                    </svg>
                    <?php if ($cartCount > 0): ?>
                         <span class="nav-cart-count"><?= $cartCount ?></span>
                    <?php endif; ?>
               </a>

               <!-- User Menu -->
               <?php if (isLoggedIn()): ?>
                    <div class="nav-user-menu" id="userMenu">
                         <button class="nav-user-btn" id="userMenuBtn" aria-expanded="false">
                              <div class="nav-avatar"><?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?></div>
                              <svg class="nav-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                   stroke="currentColor" stroke-width="2">
                                   <polyline points="6 9 12 15 18 9" />
                              </svg>
                         </button>
                         <div class="nav-dropdown" id="userDropdown">
                              <div class="nav-dropdown-header">
                                   <span class="nav-dropdown-name"><?= e($_SESSION['name'] ?? '') ?></span>
                                   <span class="nav-dropdown-email"><?= e($_SESSION['email'] ?? '') ?></span>
                              </div>
                              <div class="nav-dropdown-divider"></div>
                              <a href="/public/profile.php" class="nav-dropdown-item">
                                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                   </svg>
                                   Profil Saya
                              </a>
                              <a href="/public/orders/index.php" class="nav-dropdown-item">
                                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 11l3 3L22 4" />
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                                   </svg>
                                   Pesanan Saya
                              </a>
                              <?php if (isAdmin()): ?>
                                   <div class="nav-dropdown-divider"></div>
                                   <a href="/admin/dashboard.php" class="nav-dropdown-item nav-dropdown-item--admin">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2">
                                             <rect x="3" y="3" width="7" height="7" />
                                             <rect x="14" y="3" width="7" height="7" />
                                             <rect x="14" y="14" width="7" height="7" />
                                             <rect x="3" y="14" width="7" height="7" />
                                        </svg>
                                        Admin Panel
                                   </a>
                              <?php endif; ?>
                              <div class="nav-dropdown-divider"></div>
                              <a href="/auth/logout.php" class="nav-dropdown-item nav-dropdown-item--danger">
                                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" y1="12" x2="9" y2="12" />
                                   </svg>
                                   Keluar
                              </a>
                         </div>
                    </div>
               <?php else: ?>
                    <a href="/auth/login.php" class="btn btn--primary btn--sm">Login</a>
               <?php endif; ?>

               <!-- Hamburger -->
               <button class="nav-hamburger" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                    <span></span><span></span><span></span>
               </button>
          </div>
     </div>
</nav>