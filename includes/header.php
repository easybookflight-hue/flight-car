<?php
/* ==========================================================
   includes/header.php — Global HTML head + nav with Mega Menu
   Mobile Header: Logo + Call Now CTA + Hamburger Mega Menu
   ========================================================== */
require_once __DIR__ . '/config.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Cheap Flights US' : 'Cheap Flights US — Book Cheap International Flights' ?></title>
  <meta name="description" content="<?= isset($pageDesc) ? htmlspecialchars($pageDesc) : 'Search and book cheap flights with Cheap Flights US. Compare prices from top airlines and book with 24/7 expert support.' ?>">
  <meta name="robots" content="index, follow">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy:  { DEFAULT: '#0F172A', 700: '#1E293B', 500: '#334155' },
            sky:   { DEFAULT: '#3B82F6', 600: '#2563EB', 400: '#60A5FA' },
          },
          fontFamily: { sans: ['Inter', 'Plus Jakarta Sans', 'sans-serif'] },
          borderRadius: { '2xl': '1rem', '3xl': '1.5rem' }
        }
      }
    }
  </script>

  <!-- Google Fonts: Inter + Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/main.js" defer></script>
</head>
<body class="bg-slate-50 text-navy font-sans antialiased overflow-x-hidden">

<!-- TOP SUPPORT STRIP (DESKTOP) -->
<div class="bg-navy text-white text-xs py-2 hidden md:block border-b border-white/10">
  <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
    <div class="flex items-center gap-3 text-slate-300">
      <span class="inline-flex items-center gap-1.5 bg-white/10 px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-sky-400">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live Rates
      </span>
      <span>✈ Cheap Flights US — Compare &amp; Book Cheap Flights Worldwide</span>
    </div>
    <a href="tel:<?= SUPPORT_PHONE ?>" class="flex items-center gap-1.5 text-sky-400 font-extrabold hover:text-sky-300 transition-colors">
      <i data-lucide="phone-call" class="w-3.5 h-3.5 animate-pulse text-emerald-400"></i>
      <?= SUPPORT_PHONE ?> &nbsp;·&nbsp; <span class="text-slate-400 font-normal">24/7 Phone Support</span>
    </a>
  </div>
</div>

<!-- MAIN NAVIGATION -->
<nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 flex items-center justify-between py-2.5">

    <!-- Logo -->
    <a href="index.php" class="flex-shrink-0">
      <img src="assets/images/logo_car_rental.png" alt="Cheap Flights US Flight + Cab" class="h-10 sm:h-14 w-auto" onerror="this.src='assets/images/logo.png'">
    </a>

    <!-- Desktop Nav Links with Mega Menu -->
    <ul class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
      <li><a href="index.php" class="hover:text-navy transition-colors <?= $currentPage === 'index' ? 'text-navy font-semibold' : '' ?>">Home</a></li>

      <!-- Mega Menu Item -->
      <li class="has-megamenu py-3">
        <a href="results.php" class="flex items-center gap-1 hover:text-navy transition-colors <?= $currentPage === 'results' ? 'text-navy font-semibold' : '' ?>">
          Flights &amp; Deals
          <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
        </a>

        <!-- Mega Menu Dropdown -->
        <div class="megamenu">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-left">
            <!-- Col 1: Popular Airlines with Real Logos -->
            <div class="space-y-3">
              <h4 class="font-extrabold text-navy text-xs uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                <i data-lucide="plane" class="w-3.5 h-3.5 text-sky"></i> Top Airlines
              </h4>
              <ul class="space-y-2 text-xs text-slate-600 font-medium">
                <li>
                  <a href="results.php?from=JFK&to=DXB" class="hover:text-sky flex items-center gap-2.5 py-1">
                    <img src="https://images.kiwi.com/airlines/64x64/EK.png" alt="Emirates" class="w-5 h-5 rounded object-contain border border-slate-200 p-0.5">
                    <span>Emirates Fares</span>
                  </a>
                </li>
                <li>
                  <a href="results.php?from=JFK&to=DOH" class="hover:text-sky flex items-center gap-2.5 py-1">
                    <img src="https://images.kiwi.com/airlines/64x64/QR.png" alt="Qatar Airways" class="w-5 h-5 rounded object-contain border border-slate-200 p-0.5">
                    <span>Qatar Airways Deals</span>
                  </a>
                </li>
                <li>
                  <a href="results.php?from=JFK&to=LHR" class="hover:text-sky flex items-center gap-2.5 py-1">
                    <img src="https://images.kiwi.com/airlines/64x64/BA.png" alt="British Airways" class="w-5 h-5 rounded object-contain border border-slate-200 p-0.5">
                    <span>British Airways Fares</span>
                  </a>
                </li>
                <li>
                  <a href="results.php?from=DEL&to=BOM" class="hover:text-sky flex items-center gap-2.5 py-1">
                    <img src="https://images.kiwi.com/airlines/64x64/6E.png" alt="IndiGo" class="w-5 h-5 rounded object-contain border border-slate-200 p-0.5">
                    <span>IndiGo &amp; Air India</span>
                  </a>
                </li>
                <li>
                  <a href="results.php?from=LAX&to=HND" class="hover:text-sky flex items-center gap-2.5 py-1">
                    <img src="https://images.kiwi.com/airlines/64x64/SQ.png" alt="Singapore Airlines" class="w-5 h-5 rounded object-contain border border-slate-200 p-0.5">
                    <span>Singapore Airlines</span>
                  </a>
                </li>
              </ul>
            </div>

            <!-- Col 2: Popular Flight Routes -->
            <div class="space-y-3">
              <h4 class="font-extrabold text-navy text-xs uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sky"></i> Popular Routes
              </h4>
              <ul class="space-y-2 text-xs text-slate-600 font-medium">
                <li><a href="results.php?from=JFK&to=LHR" class="hover:text-sky flex items-center justify-between py-1"><span>New York ➔ London</span><span class="text-sky font-bold">$299</span></a></li>
                <li><a href="results.php?from=LAX&to=DXB" class="hover:text-sky flex items-center justify-between py-1"><span>Los Angeles ➔ Dubai</span><span class="text-sky font-bold">$389</span></a></li>
                <li><a href="results.php?from=ORD&to=CDG" class="hover:text-sky flex items-center justify-between py-1"><span>Chicago ➔ Paris</span><span class="text-sky font-bold">$349</span></a></li>
                <li><a href="results.php?from=MIA&to=SIN" class="hover:text-sky flex items-center justify-between py-1"><span>Miami ➔ Singapore</span><span class="text-sky font-bold">$529</span></a></li>
                <li><a href="results.php?from=SFO&to=SYD" class="hover:text-sky flex items-center justify-between py-1"><span>San Francisco ➔ Sydney</span><span class="text-sky font-bold">$649</span></a></li>
              </ul>
            </div>

            <!-- Col 3: Fare Categories with Premium Lucide Icons -->
            <div class="space-y-3">
              <h4 class="font-extrabold text-navy text-xs uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                <i data-lucide="tag" class="w-3.5 h-3.5 text-sky"></i> Fare Categories
              </h4>
              <ul class="space-y-2 text-xs text-slate-600 font-medium">
                <li><a href="results.php?cabin=business" class="hover:text-sky flex items-center gap-2 py-1"><i data-lucide="gem" class="w-3.5 h-3.5 text-indigo-500"></i> Business Class Specials</a></li>
                <li><a href="results.php?trip=round-trip" class="hover:text-sky flex items-center gap-2 py-1"><i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-500"></i> Round Trip Super Saver</a></li>
                <li><a href="results.php?cabin=first" class="hover:text-sky flex items-center gap-2 py-1"><i data-lucide="crown" class="w-3.5 h-3.5 text-amber-500"></i> First Class Deals</a></li>
                <li><a href="contact.php" class="hover:text-sky flex items-center gap-2 py-1"><i data-lucide="zap" class="w-3.5 h-3.5 text-yellow-500"></i> Last-Minute Phone Discounts</a></li>
                <li><a href="contact.php" class="hover:text-sky flex items-center gap-2 py-1"><i data-lucide="users" class="w-3.5 h-3.5 text-sky-500"></i> Family &amp; Group Discounts</a></li>
              </ul>
            </div>

            <!-- Col 4: Live Agent Phone Support Card -->
            <div class="bg-gradient-to-br from-navy to-slate-900 text-white p-4 rounded-2xl border border-white/10 space-y-3 flex flex-col justify-between shadow-md">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full overflow-hidden border border-emerald-400/40 relative flex-shrink-0">
                  <img src="assets/images/footer_sticky_agent.jpg" alt="Agent" class="w-full h-full object-cover" onerror="this.src='assets/images/agent.jpg'">
                  <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-900"></span>
                </div>
                <div>
                  <span class="bg-sky/20 text-sky-300 text-[10px] font-black px-2 py-0.5 rounded border border-sky-400/30 uppercase tracking-wider inline-block">Phone Only Rates</span>
                  <h5 class="font-extrabold text-xs text-white">Save Up to 40% Extra</h5>
                </div>
              </div>
              <p class="text-[11px] text-slate-300 leading-relaxed">Speak to our 24/7 flight experts to unlock unlisted airline fares.</p>
              <a href="tel:<?= SUPPORT_PHONE ?>" class="btn-header-cta justify-center">
                <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                Call <?= SUPPORT_PHONE ?>
              </a>
            </div>
          </div>
        </div>
      </li>

      <li><a href="cab-booking.php" class="hover:text-navy transition-colors flex items-center gap-1.5 <?= $currentPage === 'cab-booking' ? 'text-amber-600 font-bold' : '' ?>"><i data-lucide="car" class="w-4 h-4 text-amber-500"></i> Cab Booking</a></li>
      <li><a href="about.php" class="hover:text-navy transition-colors <?= $currentPage === 'about' ? 'text-navy font-semibold' : '' ?>">About Us</a></li>
      <li><a href="contact.php" class="hover:text-navy transition-colors <?= $currentPage === 'contact' ? 'text-navy font-semibold' : '' ?>">Contact</a></li>
    </ul>

    <!-- Right Side CTA: Desktop TFN + Mobile Call Now + Hamburger Menu -->
    <div class="flex items-center gap-2 sm:gap-3">
      <!-- Phone Call CTA Button -->
      <a href="tel:<?= SUPPORT_PHONE ?>" class="btn-header-cta text-xs sm:text-sm py-2 px-3 sm:py-2.5 sm:px-4">
        <i data-lucide="phone-call" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white"></i>
        <span class="hidden sm:inline"><?= SUPPORT_PHONE ?></span>
        <span class="sm:hidden font-extrabold">Call Now</span>
      </a>

      <!-- Mobile Hamburger Menu Button -->
      <button id="mobileMenuBtn" class="md:hidden p-2 rounded-xl text-navy hover:bg-slate-100 transition-colors border border-slate-200" aria-label="Toggle Menu">
        <i data-lucide="menu" class="w-5 h-5 text-navy"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Mega Menu Drawer -->
  <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-slate-200 px-4 py-4 space-y-4 shadow-xl max-h-[85vh] overflow-y-auto">
    <!-- Main Links -->
    <ul class="space-y-1 text-sm font-semibold text-slate-700">
      <li><a href="index.php" class="block py-2 px-3 rounded-xl hover:bg-slate-100 transition-colors">Home</a></li>
      <li><a href="results.php" class="block py-2 px-3 rounded-xl hover:bg-slate-100 transition-colors text-sky font-bold">Search Flights &amp; Deals</a></li>
      <li><a href="cab-booking.php" class="block py-2 px-3 rounded-xl hover:bg-amber-50 text-amber-700 font-bold transition-colors flex items-center gap-2"><i data-lucide="car" class="w-4 h-4 text-amber-500"></i> Cab Booking &amp; Rentals</a></li>
      <li><a href="about.php" class="block py-2 px-3 rounded-xl hover:bg-slate-100 transition-colors">About Us</a></li>
      <li><a href="contact.php" class="block py-2 px-3 rounded-xl hover:bg-slate-100 transition-colors">Contact Us</a></li>
    </ul>

    <!-- Mobile Mega Menu Sections -->
    <div class="space-y-3 pt-3 border-t border-slate-100">
      <!-- Airlines -->
      <div>
        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
          <i data-lucide="plane" class="w-3.5 h-3.5 text-sky"></i> Top Airlines
        </p>
        <div class="grid grid-cols-2 gap-2 text-xs font-semibold text-slate-700">
          <a href="results.php?from=JFK&to=DXB" class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
            <img src="https://images.kiwi.com/airlines/64x64/EK.png" alt="Emirates" class="w-4 h-4 rounded object-contain">
            <span>Emirates</span>
          </a>
          <a href="results.php?from=JFK&to=DOH" class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
            <img src="https://images.kiwi.com/airlines/64x64/QR.png" alt="Qatar" class="w-4 h-4 rounded object-contain">
            <span>Qatar Airways</span>
          </a>
          <a href="results.php?from=JFK&to=LHR" class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
            <img src="https://images.kiwi.com/airlines/64x64/BA.png" alt="British Airways" class="w-4 h-4 rounded object-contain">
            <span>British Airways</span>
          </a>
          <a href="results.php?from=LAX&to=HND" class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
            <img src="https://images.kiwi.com/airlines/64x64/SQ.png" alt="Singapore" class="w-4 h-4 rounded object-contain">
            <span>Singapore Air</span>
          </a>
        </div>
      </div>

      <!-- Categories -->
      <div>
        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
          <i data-lucide="tag" class="w-3.5 h-3.5 text-sky"></i> Fare Categories
        </p>
        <div class="space-y-1.5 text-xs font-semibold text-slate-700">
          <a href="results.php?cabin=business" class="flex items-center gap-2 p-2 bg-indigo-50/50 rounded-lg text-indigo-900 border border-indigo-100">
            <i data-lucide="gem" class="w-3.5 h-3.5 text-indigo-500"></i> Business Class Specials
          </a>
          <a href="results.php?trip=round-trip" class="flex items-center gap-2 p-2 bg-emerald-50/50 rounded-lg text-emerald-900 border border-emerald-100">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-500"></i> Round Trip Super Saver
          </a>
        </div>
      </div>
    </div>

    <!-- Agent Phone Call CTA in Mobile Drawer -->
    <div class="pt-2 border-t border-slate-100">
      <a href="tel:<?= SUPPORT_PHONE ?>" class="btn-header-cta w-full justify-center py-3">
        <i data-lucide="phone-call" class="w-4 h-4"></i> Call <?= SUPPORT_PHONE ?>
      </a>
    </div>
  </div>
</nav>
