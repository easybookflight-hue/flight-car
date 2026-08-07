<?php
$pageTitle = "Cookies Policy — Cheap Flights US";
$pageDesc  = "Cookies Policy of Cheap Flights US. Learn what cookies are, how we use session and persistent cookies, and how to manage your preferences.";
include __DIR__ . '/includes/header.php';
?>

<!-- HERO HEADER -->
<section class="bg-gradient-to-br from-navy via-slate-900 to-sky-900 text-white py-14 relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-sky-400 bg-white/10 px-4 py-1.5 rounded-full border border-white/20 mb-3">
      <i data-lucide="cookie" class="w-3.5 h-3.5"></i> Browsing Experience
    </div>
    <h1 class="text-3xl md:text-5xl font-black mb-3">Cookies Policy</h1>
    <p class="text-sky-200 text-base md:text-lg max-w-2xl mx-auto">How Cookies Improve Your Flight Search Experience at Cheap Flights US.</p>
    <div class="flex items-center justify-center gap-2 text-xs text-slate-300 mt-4">
      <a href="/" class="hover:text-white">Home</a>
      <span>/</span>
      <span class="text-sky-400 font-semibold">Cookies Policy</span>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="py-14">
  <div class="max-w-4xl mx-auto px-4 bg-white p-8 md:p-12 rounded-3xl border border-slate-200 shadow-sm space-y-8 text-slate-700 text-sm leading-relaxed">

    <div>
      <h2 class="text-2xl font-black text-navy mb-3">Cheap Flights US Cookies Usage</h2>
      <p class="text-slate-600">
        <strong>Cheap Flights US</strong> uses cookies to ensure that you have the best possible browsing experience. This Cookies Policy explains what cookies are, how we use them, and how you can manage your preferences.
      </p>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-2">What are Cookies?</h3>
      <p class="text-slate-600">
        Cookies are small text files stored on your device when you visit our website. They contain information that lets us remember your browsing behavior, travel search choices, and preferences to curate a seamless flight booking experience.
      </p>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-3">Types of Cookies Used by Cheap Flights US</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
          <h4 class="font-extrabold text-navy text-sm mb-1">Session Cookies</h4>
          <p class="text-xs text-slate-600 leading-relaxed">Temporary cookies deleted when you close your browser. Used to track active flight search sessions &amp; checkout steps.</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
          <h4 class="font-extrabold text-navy text-sm mb-1">Persistent Cookies</h4>
          <p class="text-xs text-slate-600 leading-relaxed">Remain on your device for longer to store language, currency preferences (USD), and past origin choices.</p>
        </div>
      </div>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-2">Why Do We Use Cookies?</h3>
      <ul class="list-disc pl-5 space-y-2 text-slate-600">
        <li><strong>Personalization:</strong> Remember your travel preferences and flight filters.</li>
        <li><strong>Security:</strong> Detect unauthorized account access and prevent fraudulent transactions.</li>
        <li><strong>Analytics:</strong> Collect anonymous data on website interactions via Google Analytics to enhance service performance.</li>
        <li><strong>Advertising:</strong> Deliver targeted travel promotions tailored to your search interests.</li>
      </ul>
    </div>

    <div>
      <h3 class="text-lg font-extrabold text-navy mb-2">How Can You Manage Your Cookie Preferences?</h3>
      <p class="text-slate-600">
        You can adjust your browser settings to reject all cookies or prompt you before accepting a cookie. Deleting or disabling cookies may impact your ability to use certain features on our flight booking engine.
      </p>
    </div>

    <div class="border-t border-slate-200 pt-6">
      <h3 class="text-lg font-extrabold text-navy mb-2">Questions About Cookies?</h3>
      <p class="text-slate-600 text-xs">
        Phone: <strong><?= SUPPORT_PHONE ?></strong> &nbsp;·&nbsp; Email: <strong><?= SUPPORT_EMAIL ?></strong>
      </p>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
