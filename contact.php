<?php
$pageTitle = "Contact Us — Cheap Flights US";
$pageDesc  = "Contact Cheap Flights US travel experts for flight bookings, itinerary modifications, and 24/7 customer assistance.";
include __DIR__ . '/includes/header.php';
?>

<!-- HERO HEADER -->
<section class="bg-gradient-to-br from-navy via-slate-900 to-sky-900 text-white py-16 relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-sky-400 bg-white/10 px-4 py-1.5 rounded-full border border-white/20 mb-4">
      <i data-lucide="headphones" class="w-3.5 h-3.5"></i> We Are Here To Help 24/7
    </div>
    <h1 class="text-3xl md:text-5xl font-black mb-3">Get in Touch With Us</h1>
    <p class="text-sky-200 text-lg md:text-xl max-w-2xl mx-auto">Have a question or need urgent flight assistance? Our travel experts are available around the clock.</p>
    <div class="flex items-center justify-center gap-2 text-xs text-slate-300 mt-4">
      <a href="/" class="hover:text-white">Home</a>
      <span>/</span>
      <span class="text-sky-400 font-semibold">Contact Us</span>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="py-16">
  <div class="max-w-6xl mx-auto px-4 space-y-12">

    <!-- Contact Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Phone Card -->
      <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm text-center space-y-3 hover:shadow-md transition-all">
        <div class="w-14 h-14 rounded-2xl bg-sky/10 text-sky flex items-center justify-center mx-auto text-xl font-bold">
          <i data-lucide="phone-call" class="w-7 h-7"></i>
        </div>
        <h3 class="font-extrabold text-navy text-lg">Call Us Toll-Free</h3>
        <p class="text-slate-500 text-xs">24/7 Live Assistance for Bookings &amp; Inquiries</p>
        <a href="tel:<?= SUPPORT_PHONE ?>" class="inline-block text-xl font-black text-sky hover:underline"><?= SUPPORT_PHONE ?></a>
      </div>

      <!-- Email Card -->
      <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm text-center space-y-3 hover:shadow-md transition-all">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold">
          <i data-lucide="mail" class="w-7 h-7"></i>
        </div>
        <h3 class="font-extrabold text-navy text-lg">Email Support</h3>
        <p class="text-slate-500 text-xs">Send us your queries &amp; booking requests</p>
        <a href="mailto:<?= SUPPORT_EMAIL ?>" class="inline-block text-base font-bold text-emerald-600 hover:underline"><?= SUPPORT_EMAIL ?></a>
      </div>

      <!-- Address Card -->
      <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm text-center space-y-3 hover:shadow-md transition-all">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto text-xl font-bold">
          <i data-lucide="map-pin" class="w-7 h-7"></i>
        </div>
        <h3 class="font-extrabold text-navy text-lg">Administrative Office</h3>
        <p class="text-slate-500 text-xs">Cheap Flights US</p>
        <p class="text-slate-700 text-xs font-semibold leading-relaxed">
          1150 NW 72ND AVE TOWER 1 STE 455 #14940, MIAMI, FL 33126
        </p>
      </div>

    </div>

    <!-- Contact Form & Detail Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
      
      <!-- Left Info -->
      <div class="space-y-6">
        <h2 class="text-2xl md:text-3xl font-black text-navy leading-tight">At Cheap Flights US, Our Experts Search Around To Find You The Best Fares</h2>
        <p class="text-slate-600 leading-relaxed">
          Be it a short weekend getaway, a long vacation, or serious business travel, you can rely on us to get you the offer that suits your requirements and finances.
        </p>

        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">✓</div>
            <p class="text-sm font-semibold text-slate-700">Dedicated Travel Specialists available 24/7</p>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">✓</div>
            <p class="text-sm font-semibold text-slate-700">Instant assistance for last minute flight booking</p>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">✓</div>
            <p class="text-sm font-semibold text-slate-700">Fast &amp; secure response to all booking inquiries</p>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-sky/10 border border-sky/20">
          <h4 class="font-extrabold text-navy text-sm mb-1">Follow Us!</h4>
          <p class="text-xs text-slate-600">Let us know what you want and our travel department will respond with an itinerary that is too good to be true!</p>
        </div>
      </div>

      <!-- Right Form -->
      <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-lg space-y-6">
        <h3 class="text-xl font-extrabold text-navy mb-2">Get In Touch With Us</h3>
        <p class="text-xs text-slate-500 mb-4">In case of any questions/queries or last minute requests, fill out the form below:</p>

        <form onsubmit="event.preventDefault(); alert('Thank you! Your message has been received. Our travel agent will call or email you shortly.'); this.reset();" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Your Full Name *</label>
            <input type="text" placeholder="John Doe" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-sky">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
            <input type="email" placeholder="john@example.com" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-sky">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number *</label>
            <input type="tel" placeholder="+1 (888) 885-5061" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-sky">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Your Message / Travel Request *</label>
            <textarea rows="4" placeholder="Tell us your origin, destination, travel dates, or questions..." required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-sky"></textarea>
          </div>
          <button type="submit" class="w-full py-3.5 rounded-xl font-extrabold text-white text-sm shadow-md transition-all" style="background:#1a6bff">
            Send Message Now
          </button>
        </form>
      </div>

    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
