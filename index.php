<?php
$pageTitle = 'Book Cheap International Flights';
$pageDesc  = 'Search and compare real-time international flight prices from top airlines. Find the best deals on flights to London, Dubai, Paris, Tokyo, Singapore and more.';
require_once __DIR__ . '/includes/header.php';

$popularDests = [
  ['London',    'LHR', 'United Kingdom', 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=600&q=80', '$349'],
  ['Dubai',     'DXB', 'United Arab Emirates', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=600&q=80', '$419'],
  ['Paris',     'CDG', 'France', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=600&q=80', '$389'],
  ['Tokyo',     'NRT', 'Japan', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=600&q=80', '$549'],
  ['Singapore', 'SIN', 'Singapore', 'https://images.unsplash.com/photo-1508964942454-1a56651d54ac?auto=format&fit=crop&w=600&q=80', '$479'],
  ['Bangkok',   'BKK', 'Thailand', 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=600&q=80', '$429'],
  ['New York',  'JFK', 'United States', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?auto=format&fit=crop&w=600&q=80', '$289'],
  ['Sydney',    'SYD', 'Australia', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?auto=format&fit=crop&w=600&q=80', '$699'],
];

$deals = [
  ['JFK','LHR','New York','London',    '2026-09-15','from $299','10% OFF'],
  ['LAX','DXB','Los Angeles','Dubai',  '2026-09-20','from $389','Sale'],
  ['ORD','CDG','Chicago','Paris',      '2026-10-05','from $349','Best Deal'],
  ['MIA','SIN','Miami','Singapore',    '2026-10-10','from $529','Limited'],
  ['JFK','NRT','New York','Tokyo',     '2026-10-15','from $499','Hot'],
  ['SFO','SYD','San Francisco','Sydney','2026-11-01','from $649','Special'],
];
?>

<!-- ═══ HERO SECTION ══════════════════════════════════════════ -->
<section class="relative min-h-[600px] flex items-center py-20" style="background:linear-gradient(160deg,#0F172A 0%,#1E3A5F 55%,#1A4A7A 100%);">
  <!-- Background pattern -->
  <div class="absolute inset-0 overflow-hidden opacity-10">
    <svg class="absolute right-0 top-0 w-full h-full" viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
      <circle cx="600" cy="100" r="280" fill="#3B82F6" fill-opacity=".4"/>
      <circle cx="200" cy="500" r="180" fill="#60A5FA" fill-opacity=".2"/>
    </svg>
  </div>

  <div class="max-w-7xl mx-auto px-4 relative w-full">
    <div class="text-center mb-10">
      <div class="section-tag text-white bg-white/10 border border-white/20 mb-4">✈ International Flight Deals</div>
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4">
        Compare &amp; Book<br>
        <span class="text-sky-400">Cheap Flights</span> Worldwide
      </h1>
      <p class="text-slate-300 text-lg md:text-xl max-w-2xl mx-auto">
        Real-time prices from top airlines and OTAs. Best fares on international routes — guaranteed.
      </p>
    </div>

    <!-- ═══ SEARCH FORM ════════════════════════════════════ -->
    <div class="search-box max-w-4xl mx-auto shadow-2xl">
      <!-- Trip type pills -->
      <div class="inline-flex items-center bg-slate-100 p-1 rounded-xl mb-4 gap-1">
        <button type="button" class="trip-pill active text-xs font-bold px-4 py-1.5 rounded-lg transition-all" data-type="one-way">One Way</button>
        <button type="button" class="trip-pill text-xs font-semibold text-slate-600 px-4 py-1.5 rounded-lg transition-all hover:text-navy" data-type="round-trip">Round Trip</button>
        <input type="hidden" id="tripType" value="one-way">
      </div>

      <form id="searchForm" novalidate>
        <!-- ROW 1: From & To with Centered Swap Button -->
        <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-2.5 items-end mb-3">
          <!-- From -->
          <div class="search-field-group relative">
            <label for="originInput">From</label>
            <i data-lucide="map-pin" class="field-icon"></i>
            <input type="text" id="originInput" placeholder="City or Airport" autocomplete="off">
            <input type="hidden" id="originCode">
            <div class="autocomplete-dropdown" id="originDropdown"></div>
          </div>

          <!-- Swap Button Centered -->
          <div class="flex items-center justify-center pb-0.5">
            <button type="button" class="swap-btn" id="swapBtn" title="Swap Origin & Destination">
              <i data-lucide="arrow-left-right" class="w-3.5 h-3.5 text-slate-500"></i>
            </button>
          </div>

          <!-- To -->
          <div class="search-field-group relative">
            <label for="destInput">To</label>
            <i data-lucide="map-pin" class="field-icon"></i>
            <input type="text" id="destInput" placeholder="City or Airport" autocomplete="off">
            <input type="hidden" id="destCode">
            <div class="autocomplete-dropdown" id="destDropdown"></div>
          </div>
        </div>

        <!-- ROW 2: Dates (Depart & Return) + Travelers -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3" id="datesRow">
          <!-- Depart -->
          <div class="search-field-group">
            <label for="depDate">Depart</label>
            <i data-lucide="calendar" class="field-icon"></i>
            <input type="date" id="depDate">
          </div>

          <!-- Return Date (Side-by-side with Depart) -->
          <div class="search-field-group" id="retDateBox" style="display:none">
            <label for="retDate">Return Date</label>
            <i data-lucide="calendar" class="field-icon"></i>
            <input type="date" id="retDate">
          </div>

          <!-- Travelers & Cabin -->
          <div class="search-field-group">
            <label>Travelers &amp; Cabin</label>
            <div class="relative">
              <i data-lucide="users" class="field-icon"></i>
              <button type="button" onclick="document.getElementById('paxDropdown').classList.toggle('hidden')"
                class="w-full text-left border border-slate-200 rounded-[0.75rem] bg-slate-50 px-3 py-2 pl-9 text-xs font-semibold text-navy outline-none focus:border-sky truncate h-[42px]" id="paxBtn">
                1 Traveler · Economy
              </button>

              <!-- TRAVELERS & CABIN POPUP -->
              <div id="paxDropdown" class="hidden absolute top-full right-0 mt-2 bg-white border border-slate-200 rounded-2xl shadow-hover z-50 p-5 w-full md:w-[320px]">
                
                <!-- Adults -->
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                  <div>
                    <p class="font-bold text-navy text-sm">Adults</p>
                    <p class="text-[11px] text-slate-400">Age 12+</p>
                  </div>
                  <div class="flex items-center gap-2.5">
                    <button type="button" onclick="changePax('adults', -1)" class="w-7 h-7 rounded-full border border-slate-200 flex items-center justify-center font-bold text-base text-slate-600 hover:border-sky hover:text-sky transition-colors">−</button>
                    <span id="adultsCount" class="font-bold text-sm text-navy w-4 text-center">1</span>
                    <button type="button" onclick="changePax('adults', 1)" class="w-7 h-7 rounded-full border border-slate-200 flex items-center justify-center font-bold text-base text-slate-600 hover:border-sky hover:text-sky transition-colors">+</button>
                  </div>
                </div>

                <!-- Children -->
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                  <div>
                    <p class="font-bold text-navy text-sm">Children</p>
                    <p class="text-[11px] text-slate-400">Ages 2-11</p>
                  </div>
                  <div class="flex items-center gap-2.5">
                    <button type="button" onclick="changePax('children', -1)" class="w-7 h-7 rounded-full border border-slate-200 flex items-center justify-center font-bold text-base text-slate-600 hover:border-sky hover:text-sky transition-colors">−</button>
                    <span id="childrenCount" class="font-bold text-sm text-navy w-4 text-center">0</span>
                    <button type="button" onclick="changePax('children', 1)" class="w-7 h-7 rounded-full border border-slate-200 flex items-center justify-center font-bold text-base text-slate-600 hover:border-sky hover:text-sky transition-colors">+</button>
                  </div>
                </div>

                <!-- Infants -->
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                  <div>
                    <p class="font-bold text-navy text-sm">Infants</p>
                    <p class="text-[11px] text-slate-400">Under 2, on lap</p>
                  </div>
                  <div class="flex items-center gap-2.5">
                    <button type="button" onclick="changePax('infants', -1)" class="w-7 h-7 rounded-full border border-slate-200 flex items-center justify-center font-bold text-base text-slate-600 hover:border-sky hover:text-sky transition-colors">−</button>
                    <span id="infantsCount" class="font-bold text-sm text-navy w-4 text-center">0</span>
                    <button type="button" onclick="changePax('infants', 1)" class="w-7 h-7 rounded-full border border-slate-200 flex items-center justify-center font-bold text-base text-slate-600 hover:border-sky hover:text-sky transition-colors">+</button>
                  </div>
                </div>

                <!-- Cabin Class -->
                <div class="pt-3">
                  <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Cabin Class</label>
                  <select id="cabinClass" onchange="updateTravelersLabel()" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-navy outline-none focus:border-sky bg-slate-50 mb-4 field-select">
                    <option value="economy">Economy</option>
                    <option value="premium_economy">Premium Economy</option>
                    <option value="business">Business</option>
                    <option value="first">First Class</option>
                  </select>
                </div>

                <button type="button" onclick="document.getElementById('paxDropdown').classList.add('hidden')" class="w-full bg-navy text-white text-xs font-bold py-2.5 rounded-xl hover:bg-slate-800 transition-colors shadow-sm">Done</button>
              </div>
            </div>
            <input type="hidden" id="adultsInput" value="1">
            <input type="hidden" id="childrenInput" value="0">
            <input type="hidden" id="infantsInput" value="0">
          </div>
        </div>

        <!-- ROW 3: Customer Contact Info (Name + Phone) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4 pt-3 border-t border-slate-100">
          <div class="search-field-group relative">
            <label for="contactName">Customer Name</label>
            <i data-lucide="user" class="field-icon"></i>
            <input type="text" id="contactName" placeholder="Full Customer Name" autocomplete="name" required>
          </div>

          <div class="search-field-group">
            <label for="contactPhone">Phone Number</label>
            <div class="phone-input-wrap">
              <select id="countryCode">
                <option value="+1" selected>🇺🇸 USA (+1)</option>
                <option value="+44">🇬🇧 UK (+44)</option>
                <option value="+91">🇮🇳 IN (+91)</option>
                <option value="+1">🇨🇦 CA (+1)</option>
                <option value="+61">🇦🇺 AU (+61)</option>
                <option value="+971">🇦🇪 UAE (+971)</option>
                <option value="+49">🇩🇪 DE (+49)</option>
                <option value="+33">🇫🇷 FR (+33)</option>
              </select>
              <input type="tel" id="contactPhone" placeholder="10-digit Phone Number" maxlength="10" autocomplete="tel" required>
            </div>
          </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div>
          <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white font-extrabold text-sm py-3 rounded-xl shadow-md shadow-blue-500/25 transition-all duration-200 hover:scale-[1.005] active:scale-[0.995] flex items-center justify-center gap-2 h-[44px]">
            <i data-lucide="search" class="w-4 h-4"></i>
            Search Flights
          </button>
        </div>
      </form>
    </div>

    <!-- Trust badges -->
    <div class="flex flex-wrap justify-center gap-6 mt-8 text-slate-300 text-sm">
      <span class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-green-400"></i> Secure Booking</span>
      <span class="flex items-center gap-1.5"><i data-lucide="zap" class="w-4 h-4 text-yellow-400"></i> Real-time Prices</span>
      <span class="flex items-center gap-1.5"><i data-lucide="globe" class="w-4 h-4 text-sky-400"></i> 500+ Airlines</span>
      <span class="flex items-center gap-1.5"><i data-lucide="headphones" class="w-4 h-4 text-purple-400"></i> 24/7 Support</span>
    </div>
  </div>
</section>

<!-- ═══ POPULAR DESTINATIONS (INFINITE RESPONSIVE CAROUSEL: 4.1 DESKTOP / 1.2 MOBILE) ════════════════════ -->
<section class="py-20 bg-gradient-to-b from-slate-50 to-white overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 relative">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
      <div>
        <span class="section-tag mb-2 inline-block">Top Picks</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-navy leading-tight">Popular International Destinations</h2>
        <p class="text-slate-500 text-sm mt-1">Explore top global routes. Scroll or click arrows to browse deals.</p>
      </div>
      <!-- Carousel controls -->
      <div class="flex items-center gap-3 self-end">
        <button id="destPrevBtnPhp" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-navy hover:bg-sky hover:text-white hover:border-sky transition-all duration-200 active:scale-95">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button id="destNextBtnPhp" class="w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-navy hover:bg-sky hover:text-white hover:border-sky transition-all duration-200 active:scale-95">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>

    <!-- Carousel Container -->
    <div class="carousel-container" id="destCarouselContainerPhp">
      <div class="carousel-track" id="destGridPhp">
        <?php foreach ($popularDests as $d): ?>
        <?php $q = http_build_query(['from' => 'JFK', 'from_name' => 'New York', 'to' => $d[1], 'to_name' => $d[0], 'trip' => 'one-way', 'adults' => 1, 'cabin' => 'economy']); ?>
        <div class="carousel-card flex-shrink-0">
          <a href="/results.php?<?= $q ?>" class="dest-card group block">
            <img src="<?= $d[3] ?>" alt="Flights to <?= $d[0] ?>" loading="lazy">
            <div class="dest-card-overlay">
              <div>
                <h3 class="text-white font-bold text-lg leading-tight"><?= $d[0] ?></h3>
                <p class="text-slate-300 text-xs mb-2"><?= $d[2] ?></p>
                <div class="flex items-center justify-between">
                  <span class="text-sky-400 font-bold text-sm"><?= $d[4] ?></span>
                  <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-2.5 py-1 rounded-full group-hover:bg-sky transition-colors"><?= $d[1] ?></span>
                </div>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ═══ EXCLUSIVE DEALS ═══════════════════════════════════════ -->
<section class="py-16 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-12">
      <span class="section-tag">Limited Time</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-navy mb-3">Exclusive Flight Deals</h2>
      <p class="text-slate-500 max-w-xl mx-auto">Hand-picked international deals for savvy travelers. Book before they're gone.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($deals as $d): ?>
      <?php $q = http_build_query(['from' => $d[0], 'from_name' => $d[2], 'to' => $d[1], 'to_name' => $d[3], 'dep' => $d[4], 'trip' => 'one-way', 'adults' => 1, 'cabin' => 'economy']); ?>
      <a href="/results.php?<?= $q ?>" class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-hover hover:-translate-y-1 transition-all duration-200 group">
        <div class="flex justify-between items-start mb-4">
          <div>
            <span class="text-xs font-bold text-sky bg-sky/10 px-2.5 py-1 rounded-full"><?= $d[6] ?></span>
          </div>
          <span class="text-xs text-slate-400"><?= date('d M Y', strtotime($d[4])) ?></span>
        </div>
        <div class="flex items-center gap-3 mb-3">
          <div class="text-center">
            <p class="text-xl font-extrabold text-navy"><?= $d[0] ?></p>
            <p class="text-xs text-slate-500"><?= $d[2] ?></p>
          </div>
          <div class="flex-1 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </div>
          <div class="text-center">
            <p class="text-xl font-extrabold text-navy"><?= $d[1] ?></p>
            <p class="text-xs text-slate-500"><?= $d[3] ?></p>
          </div>
        </div>
        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
          <span class="text-2xl font-extrabold text-navy"><?= $d[5] ?></span>
          <span class="text-xs font-semibold text-sky group-hover:text-navy transition-colors flex items-center gap-1">
            Search <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ HOW IT WORKS ══════════════════════════════════════════ -->
<section class="py-20 bg-white">
  <div class="max-w-5xl mx-auto px-4 text-center">
    <span class="section-tag">Simple Process</span>
    <h2 class="text-3xl md:text-4xl font-extrabold text-navy mb-12">Book Your Flight in 3 Steps</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <?php
      $steps = [
        ['search', 'Search Flights', 'Enter your route, dates, and passenger count. We search 500+ airlines and OTAs in real time.'],
        ['compare', 'Compare Prices', 'Compare flights by price, duration, or best value. Filter by stops, airline, or cabin class.'],
        ['credit-card', 'Book Securely', 'Select your preferred provider and book directly on the airline or OTA\'s secure website.'],
      ];
      foreach ($steps as $i => $s):
      ?>
      <div class="text-center p-6 rounded-2xl hover:bg-slate-50 transition-colors">
        <div class="w-16 h-16 bg-sky/10 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-sky transition-colors">
          <i data-lucide="<?= $s[0] ?>" class="w-7 h-7 text-sky"></i>
        </div>
        <div class="w-7 h-7 bg-navy text-white text-xs font-bold rounded-full flex items-center justify-center mx-auto -mt-2 mb-4 relative -top-2"><?= $i+1 ?></div>
        <h3 class="text-lg font-bold text-navy mb-2"><?= $s[1] ?></h3>
        <p class="text-slate-500 text-sm leading-relaxed"><?= $s[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ TRUST / STATS ═════════════════════════════════════════ -->
<section class="py-16 bg-gradient-to-r from-navy to-slate-800 text-white">
  <div class="max-w-5xl mx-auto px-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      <?php
      $stats = [
        ['2M+', 'Flights Compared'],
        ['500+', 'Airlines & OTAs'],
        ['150+', 'Countries Covered'],
        ['24/7', 'Customer Support'],
      ];
      foreach ($stats as $s):
      ?>
      <div>
        <p class="text-4xl font-extrabold text-sky-400 mb-2"><?= $s[0] ?></p>
        <p class="text-slate-300 text-sm"><?= $s[1] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ REVIEWS ═══════════════════════════════════════════════ -->
<section class="py-20 bg-slate-50">
  <div class="max-w-6xl mx-auto px-4">
    <div class="text-center mb-12">
      <span class="section-tag">Testimonials</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-navy mb-3">Trusted by Travelers Worldwide</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php
      $reviews = [
        ['Sarah M.', 'New York → London', 'Saved over $200 compared to booking directly. The real-time prices are incredibly accurate. Will use SkyFare for every trip!', 5],
        ['James T.', 'Los Angeles → Tokyo', 'Clean interface, fast search results. Found a business class deal that was $400 cheaper than competitor sites. Highly recommend.', 5],
        ['Priya K.', 'Chicago → Dubai', 'Excellent experience. Multiple booking options for the same flight — I could compare and choose the best OTA. Flew for less than expected!', 5],
      ];
      foreach ($reviews as $r):
      ?>
      <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-card hover:shadow-hover transition-shadow">
        <div class="flex items-center gap-1 mb-4">
          <?php for ($i=0;$i<$r[3];$i++): ?>
          <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
          <?php endfor; ?>
        </div>
        <p class="text-slate-600 text-sm leading-relaxed mb-5">"<?= $r[2] ?>"</p>
        <div class="border-t border-slate-100 pt-4">
          <p class="font-bold text-navy text-sm"><?= $r[0] ?></p>
          <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
            <i data-lucide="plane" class="w-3 h-3 text-sky"></i> <?= $r[1] ?>
          </p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ FAQ ═══════════════════════════════════════════════════ -->
<section class="py-20 bg-white" id="faq">
  <div class="max-w-3xl mx-auto px-4">
    <div class="text-center mb-12">
      <span class="section-tag">FAQ</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-navy mb-3">Frequently Asked Questions</h2>
    </div>
    <?php
    $faqs = [
      ['Are these real flight prices?', 'Yes. All prices are fetched in real-time from the Ignav API, which aggregates live data from airlines and major OTAs (Online Travel Agencies). Prices may change due to availability — always confirm before booking.'],
      ['Why do I see multiple prices for the same flight?', 'Different booking providers (airlines, OTAs like Expedia, Kiwi) may offer slightly different prices for the same flight due to fees, promotions, or availability windows. We show you all options.'],
      ['Are prices shown in USD?', 'Yes. All prices on SkyFare are displayed in USD for clarity and consistency, regardless of the route.'],
      ['Do you charge any booking fees?', 'SkyFare is a flight comparison platform. We do not charge booking fees. You pay the price shown by the airline or OTA directly.'],
      ['How do I book a flight?', 'Search for your route, compare results, click "Select" on a flight, then click "Continue to Book" on your preferred provider. You\'ll be redirected to their secure site to complete your booking.'],
      ['Can I find international flights?', 'Absolutely. SkyFare is focused on international routes — from New York to London, Dubai to Tokyo, and hundreds of other global routes.'],
    ];
    foreach ($faqs as $f):
    ?>
    <div class="faq-item">
      <button class="faq-btn" type="button">
        <span><?= $f[0] ?></span>
        <i data-lucide="chevron-down" class="faq-icon w-5 h-5 text-sky flex-shrink-0"></i>
      </button>
      <div class="faq-body"><?= $f[1] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/main.js?v=1.2"></script>
<script src="assets/js/search.js?v=1.2"></script>
<!-- Pre-populate if coming from footer link -->
<script>
const p = new URLSearchParams(location.search);
if (p.get('to')) {
  document.getElementById('destInput').value  = (p.get('to_name') || '') + ' (' + p.get('to') + ')';
  document.getElementById('destCode').value   = p.get('to');
}
if (p.get('from')) {
  document.getElementById('originInput').value = (p.get('from_name') || '') + ' (' + p.get('from') + ')';
  document.getElementById('originCode').value  = p.get('from');
}
// Sync pax counter display
document.getElementById('adultsInput').value = p.get('adults') || '1';
updateTravelersLabel && updateTravelersLabel();

// ── Popular Destinations Infinite Carousel JS ──
(function initDestCarouselPhp() {
  const container = document.getElementById('destCarouselContainerPhp');
  const track     = document.getElementById('destGridPhp');
  const prevBtn   = document.getElementById('destPrevBtnPhp');
  const nextBtn   = document.getElementById('destNextBtnPhp');
  if (!track || !container) return;

  let currentIndex = 0;
  let autoplayTimer = null;

  function getCardStep() {
    const card = track.querySelector('.carousel-card');
    if (!card) return 300;
    const style = window.getComputedStyle(track);
    const gap = parseFloat(style.gap) || 16;
    return card.getBoundingClientRect().width + gap;
  }

  function getVisibleCount() {
    return window.innerWidth >= 768 ? 4.1 : 1.2;
  }

  function updateSlide(transition = true) {
    track.style.transition = transition ? 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)' : 'none';
    const offset = currentIndex * getCardStep();
    track.style.transform = `translateX(-${offset}px)`;
  }

  const cards = track.querySelectorAll('.carousel-card');
  const totalCards = cards.length;

  function nextSlide() {
    const maxIndex = Math.max(0, totalCards - Math.floor(getVisibleCount()));
    if (currentIndex >= maxIndex) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }
    updateSlide();
  }

  function prevSlide() {
    const maxIndex = Math.max(0, totalCards - Math.floor(getVisibleCount()));
    if (currentIndex <= 0) {
      currentIndex = maxIndex;
    } else {
      currentIndex--;
    }
    updateSlide();
  }

  if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetAutoplay(); });
  if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetAutoplay(); });

  function startAutoplay() {
    stopAutoplay();
    autoplayTimer = setInterval(nextSlide, 3500);
  }

  function stopAutoplay() {
    if (autoplayTimer) clearInterval(autoplayTimer);
  }

  function resetAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  container.addEventListener('mouseenter', stopAutoplay);
  container.addEventListener('mouseleave', startAutoplay);
  window.addEventListener('resize', () => updateSlide(false));

  startAutoplay();
})();
</script>
