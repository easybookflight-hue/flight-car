<?php
/* ==========================================================
   thankyou.php — Flight Booking Confirmation Page
   ========================================================== */
$pageTitle = 'Booking Confirmation';
require_once __DIR__ . '/includes/header.php';
?>

<!-- THANK YOU MAIN CONTENT -->
<main class="max-w-3xl mx-auto px-4 py-12">
  <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-xl text-center relative overflow-hidden">
    <!-- Success Icon Banner -->
    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20">
      <i data-lucide="check-circle-2" class="w-10 h-10"></i>
    </div>

    <span class="inline-block bg-emerald-50 text-emerald-700 font-extrabold text-xs px-3.5 py-1 rounded-full border border-emerald-200 uppercase tracking-wider mb-3">
      ✓ Booking Request Received
    </span>

    <h1 class="text-2xl sm:text-4xl font-black text-navy mb-3">Thank You For Your Booking!</h1>
    <p class="text-slate-600 text-sm sm:text-base max-w-lg mx-auto mb-8">
      Your flight booking request has been successfully submitted and is currently being processed by our airline reservation desk.
    </p>

    <!-- Reference Code Box -->
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 max-w-md mx-auto mb-8 flex items-center justify-between">
      <div class="text-left">
        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Booking Reference PNR</p>
        <p class="text-2xl font-black text-sky-600 tracking-wider" id="tyRef">CFU-<?= rand(100000, 999999) ?></p>
      </div>
      <div class="bg-sky-50 text-sky-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-sky-200 flex items-center gap-1.5">
        <i data-lucide="clock" class="w-4 h-4"></i> Processing
      </div>
    </div>

    <!-- Flight Summary Card -->
    <div class="border border-slate-200 rounded-2xl p-5 text-left mb-8 bg-slate-50/50 space-y-4" id="tyFlightDetails">
      <h3 class="font-extrabold text-navy text-sm border-b border-slate-200 pb-2 flex items-center justify-between">
        <span>Flight Summary</span>
        <span class="text-xs font-normal text-slate-500" id="tyDate">Upcoming Flight</span>
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div>
          <p class="text-slate-400 font-semibold">Route</p>
          <p class="font-extrabold text-navy text-sm" id="tyRoute">International Flight</p>
        </div>
        <div>
          <p class="text-slate-400 font-semibold">Carrier / Airline</p>
          <p class="font-bold text-navy" id="tyAirline">Airline Partner</p>
        </div>
        <div>
          <p class="text-slate-400 font-semibold">Lead Passenger</p>
          <p class="font-bold text-navy" id="tyPaxName">Valued Customer</p>
        </div>
        <div>
          <p class="text-slate-400 font-semibold">Total Price Paid</p>
          <p class="font-extrabold text-emerald-600 text-sm" id="tyPrice">$299.00 USD</p>
        </div>
      </div>
    </div>

    <!-- Agent Phone Support Callout Card -->
    <div class="bg-gradient-to-r from-navy to-slate-900 text-white p-6 rounded-2xl border border-white/10 mb-8 text-left flex flex-col sm:flex-row items-center justify-between gap-5 shadow-lg">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-emerald-400 flex-shrink-0 bg-slate-800">
          <img src="assets/images/footer_sticky_agent.jpg" alt="Support Agent" class="w-full h-full object-cover" onerror="this.src='assets/images/agent.jpg'">
        </div>
        <div>
          <h4 class="font-extrabold text-base text-white">Need Instant E-Ticket Confirmation?</h4>
          <p class="text-xs text-slate-300 mt-0.5">Call our 24/7 customer desk to get your ticket issued immediately over the phone.</p>
        </div>
      </div>
      <a href="tel:<?= SUPPORT_PHONE ?>" class="btn-header-cta whitespace-nowrap flex-shrink-0">
        <i data-lucide="phone-call" class="w-4 h-4"></i>
        <?= SUPPORT_PHONE ?>
      </a>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <button onclick="window.print()" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-navy font-bold text-xs sm:text-sm rounded-xl transition-colors inline-flex items-center justify-center gap-2">
        <i data-lucide="printer" class="w-4 h-4"></i> Print Booking Receipt
      </button>
      <a href="index.php" class="w-full sm:w-auto px-6 py-3 bg-sky hover:bg-sky-600 text-white font-extrabold text-xs sm:text-sm rounded-xl transition-colors shadow-blue inline-flex items-center justify-center gap-2">
        <i data-lucide="search" class="w-4 h-4"></i> Search Another Flight
      </a>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const dataStr = sessionStorage.getItem('lastBooking');
  if (dataStr) {
    try {
      const data = JSON.parse(dataStr);
      const payload = data.bookingPayload || {};
      if (payload.pax_first_name || payload.pax_last_name) {
        document.getElementById('tyPaxName').textContent = `${payload.pax_first_name} ${payload.pax_last_name}`.trim();
      }
      if (payload.route) document.getElementById('tyRoute').textContent = payload.route;
      if (payload.airline) document.getElementById('tyAirline').textContent = payload.airline;
      if (payload.total_price) document.getElementById('tyPrice').textContent = `$${payload.total_price} USD`;
    } catch(e) {}
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
