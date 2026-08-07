<?php
$pageTitle = "Refund Policy — Cheap Flights US";
$pageDesc  = "Refund Policy of Cheap Flights US. Understand flight cancellation rules, waiver requests, 24-hour void windows, and processing timelines.";
include __DIR__ . '/includes/header.php';
?>

<!-- HERO HEADER -->
<section class="bg-gradient-to-br from-navy via-slate-900 to-sky-900 text-white py-14 relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-sky-400 bg-white/10 px-4 py-1.5 rounded-full border border-white/20 mb-3">
      <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Clear Policy Guidelines
    </div>
    <h1 class="text-3xl md:text-5xl font-black mb-3">Refund &amp; Cancellation Policy</h1>
    <p class="text-sky-200 text-base md:text-lg max-w-2xl mx-auto">Learn How Cancellations, Exchanges, and Refunds Work at Cheap Flights US.</p>
    <div class="flex items-center justify-center gap-2 text-xs text-slate-300 mt-4">
      <a href="/" class="hover:text-white">Home</a>
      <span>/</span>
      <span class="text-sky-400 font-semibold">Refund Policy</span>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="py-14">
  <div class="max-w-4xl mx-auto px-4 bg-white p-8 md:p-12 rounded-3xl border border-slate-200 shadow-sm space-y-8 text-slate-700 text-sm leading-relaxed">

    <div>
      <h2 class="text-2xl font-black text-navy mb-3">General Cancellation &amp; Refund Rules</h2>
      <p class="text-slate-600">
        Flight booking services offered by <strong>Cheap Flights US</strong> (including convenience &amp; service fees) are generally non-refundable per airline fare regulations. All cancellation requests must be made by calling our dedicated customer support team directly at <a href="tel:<?= SUPPORT_PHONE ?>" class="text-sky font-bold hover:underline"><?= SUPPORT_PHONE ?></a>.
      </p>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-2">Acceptance of Refund Requests</h3>
      <p class="text-slate-600 mb-3">Cheap Flights US accepts refund requests only if:</p>
      <ul class="list-disc pl-5 space-y-1 text-slate-600">
        <li>You have formally applied for cancellation &amp; refund with our customer service team.</li>
        <li>The specific airline fare rules explicitly provide for cancellation and refund.</li>
        <li>You are not marked as a "No Show" by the airline (No Show bookings forfeit refund eligibility).</li>
        <li>Waivers are granted by airline suppliers to process the cancellation request.</li>
      </ul>
    </div>

    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200">
      <h4 class="font-extrabold text-amber-900 mb-1 flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4 text-amber-600"></i> Timeline for Processing Refunds</h4>
      <p class="text-xs text-amber-800 leading-relaxed">
        While timelines vary depending on airline financial institutions, refund processing typically takes 60–90 days calculated from the date of request initiation until funds reflect in your original payment card statement.
      </p>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-2">24-Hour Cancellation Policy</h3>
      <p class="text-slate-600">
        If you cancel your flight reservation by calling our customer service team within 24 hours of booking (and prior to scheduled departure), we evaluate your request under airline void-window guidelines. Cancellation fees and airline penalties apply after the initial 24-hour void window.
      </p>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-2">Exchange and Modification Policies</h3>
      <p class="text-slate-600 mb-2">
        Confirmed non-refundable tickets cannot be refunded, but in certain cases may be exchanged for a new ticket with the same airline for a future travel date.
      </p>
      <ul class="list-disc pl-5 space-y-1 text-slate-600">
        <li>Airline penalty fees and fare differences apply to ticket exchanges.</li>
        <li>Unused segments of partially used tickets cannot be cancelled or refunded.</li>
        <li>Changes in departure dates or routing require up to 72 hours processing time.</li>
      </ul>
    </div>

    <div class="border-t border-slate-200 pt-6">
      <h3 class="text-lg font-extrabold text-navy mb-2">Need Immediate Cancellation Help?</h3>
      <p class="text-slate-600 text-xs">
        To initiate a cancellation or check refund status, call our experts directly:<br>
        Phone: <strong><?= SUPPORT_PHONE ?></strong> &nbsp;·&nbsp; Email: <strong><?= SUPPORT_EMAIL ?></strong>
      </p>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
