<!-- ═══ PRE-FOOTER CTA ═══════════════════════════════════════ -->
<section class="bg-gradient-to-r from-navy to-slate-800 text-white py-14">
  <div class="max-w-5xl mx-auto px-4 text-center">
    <p class="text-sky-400 text-sm font-semibold uppercase tracking-widest mb-3">Start Your Journey Today</p>
    <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Book Cheap Flight Tickets Online</h2>
    <p class="text-slate-300 text-lg mb-8 max-w-xl mx-auto">Compare real-time airline ticket prices and find affordable flight options across 500+ airlines.</p>
    <a href="tel:<?= SUPPORT_PHONE ?>" class="inline-flex items-center gap-3 bg-sky hover:bg-sky-600 text-white font-bold text-lg px-8 py-4 rounded-2xl shadow-blue transition-all duration-200 hover:scale-105">
      <i data-lucide="phone" class="w-5 h-5"></i>
      <?= SUPPORT_PHONE ?>
    </a>
  </div>
</section>

<!-- ═══ SITE FOOTER ══════════════════════════════════════════ -->
<footer class="bg-navy text-slate-300 pt-14 pb-8">
  <div class="max-w-7xl mx-auto px-4">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
      <!-- COL 1: THE COMPANY -->
      <div>
        <h4 class="text-white font-bold mb-5 text-base">The Company</h4>
        <ul class="space-y-3 text-sm text-slate-400">
          <li><a href="index.php" class="hover:text-sky-400 transition-colors">Home</a></li>
          <li><a href="cab-booking.php" class="hover:text-amber-400 transition-colors flex items-center gap-1.5"><i data-lucide="car" class="w-3.5 h-3.5 text-amber-400"></i> Cab Booking &amp; Rentals</a></li>
          <li><a href="about.php" class="hover:text-sky-400 transition-colors">About Us</a></li>
          <li><a href="contact.php" class="hover:text-sky-400 transition-colors">Contact Us</a></li>
          <li><a href="disclaimer.php" class="hover:text-sky-400 transition-colors">Disclaimer</a></li>
        </ul>
      </div>

      <!-- COL 2: LEGAL -->
      <div>
        <h4 class="text-white font-bold mb-5 text-base">Legal</h4>
        <ul class="space-y-3 text-sm text-slate-400">
          <li><a href="privacy.php" class="hover:text-sky-400 transition-colors">Privacy Policy</a></li>
          <li><a href="terms.php" class="hover:text-sky-400 transition-colors">Terms and Conditions</a></li>
          <li><a href="cookies.php" class="hover:text-sky-400 transition-colors">Cookies Policy</a></li>
          <li><a href="refund-policy.php" class="hover:text-sky-400 transition-colors">Refund Policy</a></li>
        </ul>
      </div>

      <!-- COL 3: QUICK LINKS -->
      <div>
        <h4 class="text-white font-bold mb-5 text-base">Contact &amp; Support</h4>
        <ul class="space-y-3 text-sm text-slate-400">
          <li><a href="tel:<?= SUPPORT_PHONE ?>" class="hover:text-sky-400 transition-colors flex items-center gap-1.5"><i data-lucide="phone" class="w-3.5 h-3.5 text-sky-400"></i> <?= SUPPORT_PHONE ?></a></li>
          <li><a href="mailto:<?= SUPPORT_EMAIL ?>" class="hover:text-sky-400 transition-colors flex items-center gap-1.5"><i data-lucide="mail" class="w-3.5 h-3.5 text-sky-400"></i> <?= SUPPORT_EMAIL ?></a></li>
          <li><span class="text-xs text-slate-400 block mt-2"><?= OFFICE_ADDRESS ?></span></li>
        </ul>
      </div>

      <!-- COL 4: STAY UPDATED -->
      <div>
        <h4 class="text-white font-bold mb-5 text-base">Stay Updated</h4>
        <p class="text-xs text-slate-400 mb-4">Follow Cheap Flights US for exclusive deals</p>
        <div class="flex gap-4">
          <a href="#" class="w-9 h-9 bg-white/10 rounded-full flex items-center justify-center hover:bg-sky transition-colors text-white"><i data-lucide="instagram" class="w-4 h-4"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 rounded-full flex items-center justify-center hover:bg-sky transition-colors text-white"><i data-lucide="facebook" class="w-4 h-4"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 rounded-full flex items-center justify-center hover:bg-sky transition-colors text-white"><i data-lucide="youtube" class="w-4 h-4"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 rounded-full flex items-center justify-center hover:bg-sky transition-colors text-white"><i data-lucide="twitter" class="w-4 h-4"></i></a>
        </div>
      </div>
    </div>

    <!-- FOOTER BOTTOM -->
    <div class="border-t border-white/10 pt-6 text-center text-xs text-slate-400">
      <p class="font-semibold text-slate-300 mb-2">Copyright &copy; <?= date('Y') ?> Cheap Flights US. All rights reserved.</p>
      <p class="max-w-4xl mx-auto text-slate-500 mb-6 leading-relaxed">
        <strong>Note:</strong> Although our website offers affordable flight prices, travel experts suggest you constantly keep checking special deals and book early. Doing so brings you the benefit of enjoying the best rates, as they keep changing as per demand for flights, time of booking, and availability of seats. Happy Travels!
      </p>

      <!-- PAYMENT ICONS -->
      <div class="flex flex-wrap justify-center items-center gap-5 text-2xl text-slate-400">
        <span class="font-extrabold text-blue-500 tracking-tighter text-lg">VISA</span>
        <span class="font-extrabold text-red-500 tracking-tighter text-lg">mastercard</span>
        <span class="font-extrabold text-sky-400 tracking-tighter text-lg">AMEX</span>
        <span class="font-extrabold text-orange-400 tracking-tighter text-lg">JCB</span>
        <span class="font-extrabold text-blue-400 tracking-tighter text-lg">PayPal</span>
        <span class="font-extrabold text-orange-500 tracking-tighter text-lg">DISCOVER</span>
        <span class="font-semibold text-green-400 text-xs flex items-center gap-1 border border-green-500/30 bg-green-500/10 px-2.5 py-1 rounded-full"><i data-lucide="lock" class="w-3.5 h-3.5"></i> SECURE ENCRYPTED</span>
      </div>
    </div>

  </div>
</footer>

<!-- ═══ STICKY BOTTOM CTA BAR ══════════════════════════════════ -->
<div class="sticky-cta-bar flex items-center justify-between shadow-2xl">
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white/20 flex-shrink-0 bg-slate-800">
      <img src="assets/images/footer_sticky_agent.jpg" alt="Agent Support" class="w-full h-full object-cover" onerror="this.src='assets/images/agent.jpg'">
    </div>
    <div class="min-w-0">
      <p class="font-extrabold text-white text-xs sm:text-sm tracking-tight leading-tight truncate">Have Questions About Cheap Flights?</p>
      <p class="sub-text text-[10px] sm:text-xs text-slate-300 truncate">Call cheapflightsus.com · 24/7 Customer Support</p>
    </div>
  </div>
  <a href="tel:<?= SUPPORT_PHONE ?>" class="flex items-center gap-1.5 text-white font-extrabold text-xs sm:text-sm px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl shadow-lg transition-all hover:scale-105 flex-shrink-0 ml-2" style="background:#1a6bff">
    <i data-lucide="phone" class="w-4 h-4"></i>
    <span><?= SUPPORT_PHONE ?></span>
  </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
</script>
</body>
</html>
