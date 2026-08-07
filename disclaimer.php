<?php
$pageTitle = "Disclaimer — Cheap Flights US";
$pageDesc  = "General Disclaimer of Cheap Flights US. Important notices regarding flight schedules, fare volatility, luggage fees, and airline check-in policies.";
include __DIR__ . '/includes/header.php';
?>

<!-- HERO HEADER -->
<section class="bg-gradient-to-br from-navy via-slate-900 to-sky-900 text-white py-14 relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-sky-400 bg-white/10 px-4 py-1.5 rounded-full border border-white/20 mb-3">
      <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Important Notices
    </div>
    <h1 class="text-3xl md:text-5xl font-black mb-3">General Disclaimer</h1>
    <p class="text-sky-200 text-base md:text-lg max-w-2xl mx-auto">Important Notices About Cheap Flights US Travel Services.</p>
    <div class="flex items-center justify-center gap-2 text-xs text-slate-300 mt-4">
      <a href="/" class="hover:text-white">Home</a>
      <span>/</span>
      <span class="text-sky-400 font-semibold">Disclaimer</span>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="py-14">
  <div class="max-w-4xl mx-auto px-4 bg-white p-8 md:p-12 rounded-3xl border border-slate-200 shadow-sm space-y-6 text-slate-700 text-sm leading-relaxed">

    <div>
      <h2 class="text-2xl font-black text-navy mb-3">Cheap Flights US Disclaimer Statement</h2>
      <p class="text-slate-600">
        <strong>Cheap Flights US</strong> is a flight booking search and travel portal that aims to enable a convenient and stress-free travel experience for you. Please take note of the important details shared below as part of our Disclaimer Policy.
      </p>
    </div>

    <div class="space-y-4">
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs font-semibold text-slate-700">• The services provided through our website are meant for personal, non-commercial travel search and booking.</p>
      </div>
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs font-semibold text-slate-700">• Flight prices displayed on our website are subject to change until your payment process &amp; ticketing are complete.</p>
      </div>
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs font-semibold text-slate-700">• Since the aviation industry is volatile, unforeseen flight schedule changes, delays, or weather cancellations may occur without prior notice from airlines.</p>
      </div>
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs font-semibold text-slate-700">• Airlines may impose extra charges for additional services such as baggage, seat selection, meals, or special assistance.</p>
      </div>
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs font-semibold text-slate-700">• Passengers are responsible for ensuring they possess valid passports, visas, and health travel certificates required for entry.</p>
      </div>
    </div>

    <div class="border-t border-slate-200 pt-6">
      <h3 class="text-lg font-extrabold text-navy mb-2">Have Questions?</h3>
      <p class="text-slate-600 text-xs">
        Phone: <strong><?= SUPPORT_PHONE ?></strong> &nbsp;·&nbsp; Email: <strong><?= SUPPORT_EMAIL ?></strong>
      </p>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
