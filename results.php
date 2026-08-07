<?php
$pageTitle = 'Search Flights';
require_once __DIR__ . '/includes/header.php';
$p = $_GET;
?>

<!-- ═══ STICKY SEARCH SUMMARY BAR ════════════════════════════ -->
<div class="sticky-search-bar">
  <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
    <div id="searchSummary" class="flex items-center gap-2 flex-wrap text-sm"></div>
    <a href="/" class="flex items-center gap-1.5 text-sky-400 text-sm font-semibold hover:text-sky-300 transition-colors whitespace-nowrap flex-shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
      Modify Search
    </a>
  </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">

  <!-- Top bar: count + sort tabs -->
  <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-navy">
        <?= htmlspecialchars(($p['from_name'] ?? $p['from'] ?? '?') . ' → ' . ($p['to_name'] ?? $p['to'] ?? '?')) ?>
      </h1>
      <p id="resultsCount" class="text-sm text-slate-500 mt-0.5">Searching live prices...</p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <span class="text-sm text-slate-500 font-medium">Sort:</span>
      <button class="sort-tab active" data-sort="time">🕒 Departure Time (00:01)</button>
      <button class="sort-tab" data-sort="cheapest">💰 Cheapest</button>
      <button class="sort-tab" data-sort="fastest">⚡ Fastest</button>
      <button class="sort-tab" data-sort="best">⭐ Best</button>
      <!-- Mobile filter toggle -->
      <button id="filterToggle" class="md:hidden sort-tab flex items-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        Filters
      </button>
    </div>
  </div>

  <!-- Layout: sidebar + results -->
  <div class="flex flex-col md:flex-row gap-6">

    <!-- ═══ FILTER SIDEBAR ════════════════════════════════════ -->
    <aside class="md:w-72 flex-shrink-0">
      <div id="filterPanel" class="hidden md:block filter-panel">
        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
          <h3 class="font-bold text-navy flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filters
          </h3>
          <button id="resetFilters" class="text-xs text-sky font-semibold hover:text-sky-600 transition-colors">Reset all</button>
        </div>

        <!-- Stops -->
        <div class="mb-6">
          <p class="filter-section-title">Stops</p>
          <div class="space-y-1">
            <label class="filter-checkbox"><input type="checkbox" class="stop-cb" value="0" checked> Non-stop</label>
            <label class="filter-checkbox"><input type="checkbox" class="stop-cb" value="1" checked> 1 Stop</label>
            <label class="filter-checkbox"><input type="checkbox" class="stop-cb" value="2" checked> 2+ Stops</label>
          </div>
        </div>

        <!-- Price range -->
        <div class="mb-6">
          <p class="filter-section-title">Max Price: <span class="text-sky font-bold" id="priceLabel">$5000</span></p>
          <input type="range" id="priceSlider" min="50" max="5000" value="5000" class="mt-2">
        </div>

        <!-- Airlines (populated by JS) -->
        <div class="mb-6">
          <p class="filter-section-title">Airlines</p>
          <div id="airlineFilters" class="space-y-1">
            <div class="skeleton h-4 rounded w-3/4"></div>
            <div class="skeleton h-4 rounded w-1/2"></div>
            <div class="skeleton h-4 rounded w-2/3"></div>
          </div>
        </div>

        <!-- Cabin class -->
        <div class="mb-2">
          <p class="filter-section-title">Cabin Class</p>
          <div class="space-y-1">
            <label class="filter-checkbox"><input type="checkbox" class="cabin-cb" value="economy" checked> Economy</label>
            <label class="filter-checkbox"><input type="checkbox" class="cabin-cb" value="premium_economy" checked> Premium Economy</label>
            <label class="filter-checkbox"><input type="checkbox" class="cabin-cb" value="business" checked> Business</label>
            <label class="filter-checkbox"><input type="checkbox" class="cabin-cb" value="first" checked> First Class</label>
          </div>
        </div>
      </div>
    </aside>

    <!-- ═══ FLIGHT RESULTS ════════════════════════════════════ -->
    <main class="flex-1 min-w-0 space-y-4" id="flightsList">
      <!-- Populated by results.js -->
      <?php /* Skeleton shown by default via JS */ ?>
    </main>
  </div>
</div>

<!-- API live price notice -->
<div class="max-w-7xl mx-auto px-4 pb-4">
  <p class="text-xs text-slate-400 flex items-center gap-1.5">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Live prices powered by Ignav API · All prices in USD · Last updated: just now
  </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/main.js?v=1.2"></script>
<script src="assets/js/search.js?v=1.2"></script>
<script src="assets/js/results.js?v=1.2"></script>
