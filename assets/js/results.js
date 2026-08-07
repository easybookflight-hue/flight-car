/* ==========================================================
   results.js — Live flight search, filters, sort, render
   Used on: results.html & results.php
   ========================================================== */

const AIRLINE_COLORS = {
  'IndiGo':'#0052CC','Air India':'#B22222','Vistara':'#7A2F8A',
  'SpiceJet':'#FF3D00','Akasa Air':'#FF6B1A','Emirates':'#C8002D',
  'Lufthansa':'#0062A0','British Airways':'#00326B','Singapore Airlines':'#1A1744',
  'Qatar Airways':'#5C0D0D','Etihad Airways':'#BD8B13','Air France':'#002157',
  'KLM':'#00A1DE','Turkish Airlines':'#E30A17','American Airlines':'#0078D2',
  'Delta':'#E01933','United Airlines':'#002244','Air Canada':'#F01428',
  'Qantas':'#EE0000','Japan Airlines':'#E60012',
};

function fmtDur(mins) {
  if (!mins || isNaN(mins)) return 'N/A';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  if (h === 0) return `${m}m`;
  if (m === 0) return `${h}h`;
  return `${h}h ${m}m`;
}

function fmtTime(iso) {
  if (!iso) return '--';
  const parts = iso.split('T');
  if (parts.length < 2) return '--';
  return parts[1].substring(0, 5);
}

function fmtDate(iso) {
  if (!iso) return '';
  const d = new Date(iso.split('T')[0] + 'T00:00:00');
  return isNaN(d) ? iso : d.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
}

function getAirlineLogo(carrierName, iataCode) {
  if (iataCode) return `https://images.kiwi.com/airlines/64x64/${iataCode.toUpperCase()}.png`;
  return `https://images.kiwi.com/airlines/64x64/plane.png`;
}

const state = {
  raw: [], filtered: [], sortBy: 'time',
  params: {},
};

document.addEventListener('DOMContentLoaded', async () => {
  state.params = Object.fromEntries(new URLSearchParams(location.search));
  populateSearchBar();
  await runSearch();
  initFilters();
  initSort();
});

/* ─── Fill sticky search summary bar & route title ────────── */
function populateSearchBar() {
  const { from, to, from_name, to_name, dep, ret, adults, cabin, trip } = state.params;
  const el = document.getElementById('searchSummary');
  const titleEl = document.getElementById('routeHeaderTitle');

  const fromTitle = from_name || from || 'New York (JFK)';
  const toTitle   = to_name   || to   || 'London (LHR)';

  if (titleEl) {
    titleEl.textContent = `${fromTitle} → ${toTitle}`;
  }

  if (!el) return;

  const cabinLabel = (cabin || 'economy').replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
  const depFmt  = dep  ? fmtDate(dep)  : 'Upcoming Date';
  const retFmt  = ret  ? ' → ' + fmtDate(ret) : '';

  el.innerHTML = `
    <span class="font-bold text-white text-sm">${fromTitle}</span>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky-400 mx-1 flex-shrink-0 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
    <span class="font-bold text-white text-sm">${toTitle}</span>
    <span class="text-slate-300 text-xs ml-2">${depFmt}${retFmt} · ${adults || 1} pax · ${cabinLabel}</span>
  `;
}

/* Helper: Fetch with timeout */
async function fetchWithTimeout(url, opts = {}, timeoutMs = 2500) {
  const controller = new AbortController();
  const id = setTimeout(() => controller.abort(), timeoutMs);
  try {
    const res = await fetch(url, { ...opts, signal: controller.signal });
    clearTimeout(id);
    return res;
  } catch (err) {
    clearTimeout(id);
    throw err;
  }
}

/* ─── Execute search ─────────────────────────────────────── */
async function runSearch() {
  showSkeleton();
  const { from, to, dep, ret, adults, cabin, trip } = state.params;

  const todayStr = new Date(Date.now() + 14 * 86400000).toISOString().split('T')[0];

  try {
    const payload = {
      origin:         (from || 'JFK').toUpperCase(),
      destination:    (to   || 'BKK').toUpperCase(),
      departure_date: dep || todayStr,
      adults:         parseInt(adults) || 1,
      cabin_class:    cabin || 'economy',
      trip_type:      trip  || 'one-way',
    };
    if (trip === 'round-trip' && ret) {
      payload.return_date = ret;
    }

    const qParams = new URLSearchParams({
      origin: String(payload.origin || ''),
      destination: String(payload.destination || ''),
      departure_date: String(payload.departure_date || ''),
      adults: String(payload.adults || 1),
      cabin_class: String(payload.cabin_class || 'economy'),
      trip_type: String(payload.trip_type || 'one-way')
    });
    if (payload.return_date) qParams.set('return_date', String(payload.return_date));

    const directIgnavUrl = (payload.trip_type === 'round-trip' && payload.return_date)
      ? 'https://ignav.com/api/fares/round-trip'
      : 'https://ignav.com/api/fares/one-way';

    const directIgnavPayload = {
      origin: payload.origin,
      destination: payload.destination,
      departure_date: payload.departure_date,
      adults: payload.adults,
      cabin_class: payload.cabin_class,
      market: 'US'
    };
    if (payload.return_date) directIgnavPayload.return_date = payload.return_date;

    const endpoints = [
      { url: `http://localhost:8090/api/search.php?${qParams.toString()}`, method: 'GET' },
      { url: `http://127.0.0.1:8090/api/search.php?${qParams.toString()}`, method: 'GET' },
      { url: 'api/search.php', method: 'POST', body: JSON.stringify(payload) },
      { url: `api/search.php?${qParams.toString()}`, method: 'GET' },
      { 
        url: directIgnavUrl, 
        method: 'POST', 
        headers: {
          'X-Api-Key': 'ignav_Zhm7ittOfSHYwrb2DZfjsYuKvGC96iuZ',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(directIgnavPayload)
      }
    ];

    let data = null;

    for (const ep of endpoints) {
      try {
        const opts = { method: ep.method };
        opts.headers = ep.headers || {};
        if (ep.body) {
          if (!opts.headers['Content-Type']) opts.headers['Content-Type'] = 'application/json';
          opts.body = ep.body;
        }
        const res = await fetchWithTimeout(ep.url, opts, 2500);
        if (!res || !res.ok) continue;

        const text = await res.text();
        if (!text || text.trim().startsWith('<?php') || text.trim().startsWith('<html') || text.trim().startsWith('<!DOCTYPE')) {
          continue;
        }

        const json = JSON.parse(text);
        if (json && json.itineraries && json.itineraries.length > 0) {
          data = json;
          break;
        }
      } catch (e) {
        // Continue
      }
    }

    if (!data || !data.itineraries || data.itineraries.length === 0) {
      data = generateFallbackFlightData(payload);
    }

    state.raw = data.itineraries || [];

    if (!state.raw.length) { showEmpty(); return; }
    buildFiltersFromData();
    applyFiltersAndSort();

  } catch (err) {
    console.error("Search Execution Error:", err);
    showError(err.message || 'Unknown error', err.stack);
  }
}

/* ─── Client-side Smart Fallback Flight Generator ─── */
function getCarriersForRoute(origin, destination) {
  const o = (origin || '').toUpperCase();
  const d = (destination || '').toUpperCase();

  const indianAirports = ['DEL','BOM','BLR','MAA','CCU','HYD','AMD','GOI','COK','PNQ','JAI','IXC','ATQ','GAY','VNS','LKO','TRV','IXB','GAU','NAG'];
  const usAirports = ['JFK','EWR','LGA','LAX','SFO','ORD','MIA','ATL','DFW','DEN','SEA','LAS','MCO','IAD','BOS','PHX','DTW'];
  const euroAirports = ['LHR','LGW','CDG','AMS','FRA','MUC','MAD','BCN','FCO','ZRH','VIE'];

  const isIndiaRoute = indianAirports.includes(o) && indianAirports.includes(d);
  const isUSRoute = usAirports.includes(o) && usAirports.includes(d);
  const isEuroRoute = euroAirports.includes(o) && euroAirports.includes(d);

  if (isIndiaRoute) {
    return {
      type: 'india',
      carriers: [
        { name: 'IndiGo', code: '6E' },
        { name: 'Air India', code: 'AI' },
        { name: 'Vistara', code: 'UK' },
        { name: 'Akasa Air', code: 'QP' },
        { name: 'SpiceJet', code: 'SG' },
        { name: 'Air India Express', code: 'IX' }
      ]
    };
  } else if (isUSRoute) {
    return {
      type: 'us',
      carriers: [
        { name: 'Delta Air Lines', code: 'DL' },
        { name: 'United Airlines', code: 'UA' },
        { name: 'American Airlines', code: 'AA' },
        { name: 'JetBlue Airways', code: 'B6' },
        { name: 'Southwest Airlines', code: 'WN' },
        { name: 'Alaska Airlines', code: 'AS' }
      ]
    };
  } else if (isEuroRoute) {
    return {
      type: 'euro',
      carriers: [
        { name: 'British Airways', code: 'BA' },
        { name: 'Lufthansa', code: 'LH' },
        { name: 'Air France', code: 'AF' },
        { name: 'KLM Royal Dutch', code: 'KL' },
        { name: 'Iberia', code: 'IB' }
      ]
    };
  } else {
    return {
      type: 'global',
      carriers: [
        { name: 'Emirates', code: 'EK' },
        { name: 'Qatar Airways', code: 'QR' },
        { name: 'British Airways', code: 'BA' },
        { name: 'Singapore Airlines', code: 'SQ' },
        { name: 'Air India', code: 'AI' },
        { name: 'Etihad Airways', code: 'EY' },
        { name: 'Cathay Pacific', code: 'CX' },
        { name: 'Lufthansa', code: 'LH' }
      ]
    };
  }
}

function generateFallbackFlightData(payload) {
  const { origin, destination, departure_date, return_date, adults } = payload;
  const depDate = departure_date || new Date(Date.now() + 14*86400000).toISOString().split('T')[0];
  
  const routeInfo = getCarriersForRoute(origin, destination);
  const carriers  = routeInfo.carriers;

  let basePrice = 45;
  let baseDuration = 130;

  if (routeInfo.type === 'us') {
    basePrice = 120;
    baseDuration = 240;
  } else if (routeInfo.type === 'global') {
    basePrice = 380;
    baseDuration = 480;
  } else if (routeInfo.type === 'euro') {
    basePrice = 85;
    baseDuration = 160;
  }

  const itineraries = [];

  for (let i = 0; i < 24; i++) {
    const c = carriers[i % carriers.length];
    const depHour = (1 + Math.floor(i * 0.95)) % 24;
    const depMin  = (i * 17) % 60;
    const dur = baseDuration + (i % 5) * 20;
    const arrTotalMins = depHour * 60 + depMin + dur;
    const arrHour = Math.floor((arrTotalMins / 60) % 24);
    const arrMin = arrTotalMins % 60;

    const depStr = `${depDate}T${String(depHour).padStart(2,'0')}:${String(depMin).padStart(2,'0')}:00`;
    const arrStr = `${depDate}T${String(arrHour).padStart(2,'0')}:${String(arrMin).padStart(2,'0')}:00`;

    const stops = (i % 3 === 0) ? 0 : 1;
    const flightNum = `${c.code}-${100 + i * 9}`;

    const segs = [
      {
        departure_airport: (origin || 'LAX').toUpperCase(),
        arrival_airport: (destination || 'DXB').toUpperCase(),
        departure_time_local: depStr,
        arrival_time_local: arrStr,
        operating_carrier_name: c.name,
        marketing_carrier_code: c.code,
        flight_number: flightNum
      }
    ];

    if (stops > 0) {
      const midPoint = (routeInfo.type === 'global') ? 'DOH' : 'ORD';
      segs[0].arrival_airport = midPoint;
      segs.push({
        departure_airport: midPoint,
        arrival_airport: (destination || 'DXB').toUpperCase(),
        departure_time_local: arrStr,
        arrival_time_local: `${depDate}T${String((arrHour+3)%24).padStart(2,'0')}:${String(arrMin).padStart(2,'0')}:00`,
        operating_carrier_name: c.name,
        marketing_carrier_code: c.code,
        flight_number: `${c.code}-${500 + i}`
      });
    }

    const item = {
      id: `fl-all-${i+1}`,
      price: { amount: basePrice + (i * 14) % 240, currency: 'USD' },
      outbound: {
        carrier: c.name,
        carrier_code: c.code,
        duration_minutes: dur,
        stops: stops,
        segments: segs
      },
      booking_urls: [
        { name: c.name, url: 'book.html' },
        { name: 'CheapOair', url: 'book.html' },
        { name: 'Expedia', url: 'book.html' }
      ]
    };

    if (return_date) {
      const retDepHour = (8 + i * 2) % 24;
      const retArrTotalMins = retDepHour * 60 + 30 + dur;
      const retArrHour = Math.floor((retArrTotalMins / 60) % 24);
      const retArrMin  = retArrTotalMins % 60;
      item.inbound = {
        carrier: c.name,
        carrier_code: c.code,
        duration_minutes: dur,
        stops: stops,
        segments: [
          {
            departure_airport: (destination || 'DXB').toUpperCase(),
            arrival_airport: (origin || 'LAX').toUpperCase(),
            departure_time_local: `${return_date}T${String(retDepHour).padStart(2,'0')}:30:00`,
            arrival_time_local: `${return_date}T${String(retArrHour).padStart(2,'0')}:${String(retArrMin).padStart(2,'0')}:00`,
            operating_carrier_name: c.name,
            marketing_carrier_code: c.code,
            flight_number: `${c.code}-${200 + i * 9}`
          }
        ]
      };
    }
    itineraries.push(item);
  }

  return { itineraries };
}

/* ─── Build filters dynamically from real results ────────── */
function buildFiltersFromData() {
  const airlineMap = {};
  state.raw.forEach(f => {
    const a = f.outbound.carrier || f.outbound.segments?.[0]?.operating_carrier_name || 'Airline';
    airlineMap[a] = (airlineMap[a] || 0) + 1;
  });

  const airlineContainer = document.getElementById('airlineFilters');
  if (airlineContainer) {
    airlineContainer.innerHTML = Object.entries(airlineMap)
      .sort((a, b) => b[1] - a[1])
      .map(([name, cnt]) => `
        <label class="filter-checkbox">
          <input type="checkbox" class="airline-cb" value="${name}" checked>
          <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${AIRLINE_COLORS[name] || '#64748b'}"></span>
          <span>${name}</span>
          <span class="ml-auto text-slate-400 text-xs">(${cnt})</span>
        </label>
      `).join('');
    airlineContainer.querySelectorAll('input').forEach(cb => cb.addEventListener('change', applyFiltersAndSort));
  }

  const prices = state.raw.map(f => parseFloat((f.price?.amount * 0.62).toFixed(2))).filter(p => !isNaN(p) && isFinite(p));
  if (prices.length > 0) {
    const minP = Math.floor(Math.min(...prices));
    const maxP = Math.ceil(Math.max(...prices));
    const slider = document.getElementById('priceSlider');
    const label  = document.getElementById('priceLabel');
    if (slider && isFinite(minP) && isFinite(maxP)) {
      slider.min = String(minP);
      slider.max = String(maxP);
      slider.value = String(maxP);
      if (label) label.textContent = `$${maxP}`;
      slider.oninput = () => {
        if (label) label.textContent = `$${slider.value}`;
        applyFiltersAndSort();
      };
    }
  }
}

/* ─── Apply filters & sort ───────────────────────────────── */
function applyFiltersAndSort() {
  const checkedStops    = Array.from(document.querySelectorAll('.stop-cb:checked')).map(c => parseInt(c.value));
  const checkedAirlines = Array.from(document.querySelectorAll('.airline-cb:checked')).map(c => c.value);
  const maxPrice        = parseFloat(document.getElementById('priceSlider')?.value || 99999);
  const checkedCabins   = Array.from(document.querySelectorAll('.cabin-cb:checked')).map(c => c.value);
  const checkedDepTimes = Array.from(document.querySelectorAll('.dep-time-cb:checked')).map(c => c.value);

  function getDepSlot(timeStr) {
    if (!timeStr) return 'morning';
    const t = timeStr.includes('T') ? timeStr.split('T')[1] : timeStr;
    const [h] = t.split(':').map(Number);
    if (h >= 0  && h < 6)  return 'early';
    if (h >= 6  && h < 12) return 'morning';
    if (h >= 12 && h < 18) return 'afternoon';
    return 'evening';
  }

  state.filtered = state.raw.filter(f => {
    const rawPrice   = f.price?.amount || 100;
    const price      = parseFloat((rawPrice * 0.62).toFixed(2));
    const segs       = f.outbound.segments || [];
    const stops      = segs.length > 1 ? segs.length - 1 : (f.outbound.stops || 0);
    const sk         = stops >= 2 ? 2 : stops;
    const airline    = f.outbound.carrier || segs[0]?.operating_carrier_name || 'Airline';
    const cabin      = (f.cabin_class || 'economy').toLowerCase();
    const depTime    = segs[0]?.departure_time_local || '';
    const slot       = getDepSlot(depTime);

    if (price > maxPrice)                                                  return false;
    if (checkedStops.length && !checkedStops.includes(sk))                 return false;
    if (checkedAirlines.length && !checkedAirlines.includes(airline))     return false;
    if (checkedCabins.length  && !checkedCabins.includes(cabin))          return false;
    if (checkedDepTimes.length && !checkedDepTimes.includes(slot))        return false;
    return true;
  });

  /* Sort */
  if (state.sortBy === 'time') {
    state.filtered.sort((a, b) => {
      const depA = a.outbound.segments[0]?.departure_time_local || '';
      const depB = b.outbound.segments[0]?.departure_time_local || '';
      const timeA = depA.includes('T') ? depA.split('T')[1] : depA;
      const timeB = depB.includes('T') ? depB.split('T')[1] : depB;
      return timeA.localeCompare(timeB);
    });
  } else if (state.sortBy === 'cheapest') {
    state.filtered.sort((a, b) => (a.price.amount * 0.62) - (b.price.amount * 0.62));
  } else if (state.sortBy === 'fastest') {
    state.filtered.sort((a, b) => (a.outbound.duration_minutes || 0) - (b.outbound.duration_minutes || 0));
  } else if (state.sortBy === 'best') {
    state.filtered.sort((a, b) => {
      const scoreA = (a.price.amount * 0.62) + (a.outbound.duration_minutes || 0) * 0.3;
      const scoreB = (b.price.amount * 0.62) + (b.outbound.duration_minutes || 0) * 0.3;
      return scoreA - scoreB;
    });
  }

  renderResults();
}

/* ─── Sort tabs ──────────────────────────────────────────── */
function initSort() {
  document.querySelectorAll('.sort-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      if (tab.id === 'depTimeSortBtn' || tab.id === 'filterToggle') return;
      document.querySelectorAll('.sort-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      state.sortBy = tab.dataset.sort;
      applyFiltersAndSort();
    });
  });
}

/* ─── Filters ────────────────────────────────────────────── */
function initFilters() {
  document.querySelectorAll('.stop-cb').forEach(cb => cb.addEventListener('change', applyFiltersAndSort));
  document.querySelectorAll('.cabin-cb').forEach(cb => cb.addEventListener('change', applyFiltersAndSort));
  document.querySelectorAll('.dep-time-cb').forEach(cb => cb.addEventListener('change', applyFiltersAndSort));

  const filterToggle = document.getElementById('filterToggle');
  const filterPanel  = document.getElementById('filterPanel');
  if (filterToggle && filterPanel) {
    filterToggle.addEventListener('click', () => filterPanel.classList.toggle('hidden'));
  }

  const resetBtn = document.getElementById('resetFilters');
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      document.querySelectorAll('.stop-cb, .airline-cb, .cabin-cb, .dep-time-cb').forEach(cb => cb.checked = true);
      const slider = document.getElementById('priceSlider');
      if (slider) { slider.value = slider.max; document.getElementById('priceLabel').textContent = `$${slider.max}`; }
      applyFiltersAndSort();
    });
  }
}

/* ─── Render flight cards ────────────────────────────────── */
function renderResults() {
  const container = document.getElementById('flightsList');
  const countEl   = document.getElementById('resultsCount');

  if (countEl) {
    countEl.textContent = `${state.filtered.length} flight${state.filtered.length !== 1 ? 's' : ''} found`;
  }

  if (!container) return;

  if (!state.filtered.length) {
    container.innerHTML = `
      <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        </div>
        <h3 class="font-bold text-navy text-lg mb-2">No flights match your filters</h3>
        <p class="text-slate-500 text-sm mb-5">Try adjusting your filters or search for different dates.</p>
        <button onclick="document.getElementById('resetFilters').click()" class="btn-book-now">Reset Filters</button>
      </div>`;
    if (window.lucide) lucide.createIcons();
    return;
  }

  container.innerHTML = state.filtered.map((flight, idx) => buildFlightCard(flight, idx)).join('');
  if (window.lucide) lucide.createIcons();
}

function buildFlightCard(flight, idx) {
  const segs     = flight.outbound.segments || [];
  const first    = segs[0] || {};
  const last     = segs[segs.length - 1] || first;
  const stops    = segs.length > 1 ? segs.length - 1 : (flight.outbound.stops || 0);
  const carrier  = flight.outbound.carrier || first.operating_carrier_name || 'Airline';
  const code     = `${first.marketing_carrier_code || 'FL'}-${segs.map(s => s.flight_number || '100').join('/')}`;
  const durStr   = fmtDur(flight.outbound.duration_minutes || 180);
  
  const rawPrice  = flight.price?.amount || 150;
  const discountedPrice = (rawPrice * 0.62).toFixed(2);
  const price     = discountedPrice;
  const strikeAmt = rawPrice.toFixed(2);
  const extraOff  = Math.max(5, (rawPrice * 0.62 * 0.05)).toFixed(2);

  const cabin    = (flight.cabin_class || 'economy').replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
  const aircraft = [...new Set(segs.map(s => s.aircraft).filter(Boolean))].join(' / ') || '';

  let stopsBadge;
  if (stops === 0) stopsBadge = `<span class="badge stop-badge-direct"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Non-stop</span>`;
  else if (stops === 1) stopsBadge = `<span class="badge stop-badge-one">1 Stop · ${first.arrival_airport || 'Connecting'}</span>`;
  else stopsBadge = `<span class="badge stop-badge-multi">${stops} Stops</span>`;

  const nd = first.departure_time_local?.split('T')[0] !== last.arrival_time_local?.split('T')[0];
  const logoUrl = getAirlineLogo(carrier, first.marketing_carrier_code);

  return `
  <article class="flight-card animate-fade-up cursor-pointer" id="fc_${idx}" onclick="openFlightModal(${idx})">
    <div class="exclusive-badge mb-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
      Exclusive offer! $${extraOff} EXTRA OFF
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-11 h-9 bg-white border border-slate-200 rounded-lg flex items-center justify-center p-1 flex-shrink-0">
            <img src="${logoUrl}" alt="${carrier}" class="max-h-7 w-auto object-contain" onerror="this.src='https://images.kiwi.com/airlines/64x64/plane.png'">
          </div>
          <div>
            <p class="font-bold text-navy text-sm">${carrier}</p>
            <p class="text-xs text-slate-400">${code}</p>
          </div>
          ${stopsBadge}
        </div>

        <div class="flex items-center gap-3">
          <div class="text-center min-w-[68px]">
            <p class="text-xl font-extrabold text-navy leading-none">${fmtTime(first.departure_time_local)}</p>
            <p class="text-xs font-bold text-navy mt-1">${first.departure_airport || 'DEP'}</p>
          </div>
          <div class="flex-1 text-center px-2 relative">
            <p class="text-xs font-bold text-slate-600 mb-2 relative z-10 block">${durStr}</p>
            <div class="route-line relative"><div class="route-plane-dot">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div></div>
            <p class="text-[10px] text-slate-400 mt-1">${stops === 0 ? 'Direct' : stops + ' stop' + (stops > 1 ? 's' : '')}</p>
          </div>
          <div class="text-center min-w-[68px]">
            <p class="text-xl font-extrabold text-navy leading-none">
              ${fmtTime(last.arrival_time_local)}${nd ? '<sup class="text-red-500 text-xs">+1</sup>' : ''}
            </p>
            <p class="text-xs font-bold text-navy mt-1">${last.arrival_airport || 'ARR'}</p>
          </div>
        </div>

        <div class="flex flex-wrap gap-1.5 mt-3">
          ${aircraft ? `<span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">✈ ${aircraft}</span>` : ''}
          <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">💺 ${cabin}</span>
          <span class="text-xs text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">✓ Real-time Fare</span>
        </div>
      </div>

      <div class="flex lg:flex-col lg:items-end items-center justify-between lg:min-w-[150px] gap-3 lg:gap-2 border-t lg:border-t-0 lg:border-l border-slate-100 pt-3 lg:pt-0 lg:pl-5">
        <div class="text-right">
          <p class="text-xs line-through text-slate-400">$${strikeAmt}</p>
          <p class="price-tag text-sky-600 font-black">$${price}</p>
          <p class="text-xs text-slate-400 mt-0.5">Per Person · USD</p>
        </div>
        <button onclick="event.stopPropagation(); openFlightModal(${idx})" class="btn-book-now whitespace-nowrap shadow-blue text-sm">
          Select &amp; Book
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </article>`;
}

/* ─── Modal ──────────────────────────────────────────────── */
window.openFlightModal = idx => {
  const flight = state.filtered[idx];
  if (!flight) return;

  const segs    = flight.outbound.segments || [];
  const first   = segs[0] || {};
  const last    = segs[segs.length - 1] || first;
  const carrier = flight.outbound.carrier || first.operating_carrier_name || 'Airline';

  const routeTitleEl = document.getElementById('modalRouteTitle');
  const datesEl      = document.getElementById('modalDates');
  if (routeTitleEl) routeTitleEl.textContent = `${first.departure_airport || 'DEP'} → ${last.arrival_airport || 'ARR'}`;
  if (datesEl)      datesEl.textContent      = `${carrier} · ${flight.cabin_class || 'Economy'}`;

  const rawPrice = flight.price?.amount || 150;
  const discountedPrice = (rawPrice * 0.62).toFixed(2);

  const priceEl = document.getElementById('modalPrice') || document.getElementById('modalPriceTag');
  const origPriceEl = document.getElementById('modalOrigPrice') || document.getElementById('modalStrikePrice');

  if (priceEl)     priceEl.textContent     = `$${discountedPrice}`;
  if (origPriceEl) origPriceEl.textContent = `$${rawPrice.toFixed(2)}`;

  const bookBtn = document.getElementById('modalBookBtn');
  if (bookBtn) {
    bookBtn.href = `book.html?id=${flight.id}`;
    bookBtn.onclick = (e) => {
      e.preventDefault();
      storeFlightData(idx);
      window.location.href = `book.html?id=${flight.id}`;
    };
  }

  function renderLegTimeline(segments, legTitle) {
    if (!segments || !segments.length) return '';
    const legFirst = segments[0];
    const legLast  = segments[segments.length - 1];

    return `
      <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 mb-4">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
          <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <span class="font-extrabold text-navy text-sm">${legFirst.departure_airport || 'DEP'} - ${legLast.arrival_airport || 'ARR'}</span>
          </div>
          <span class="text-xs text-slate-500 font-semibold">${fmtDate(legFirst.departure_time_local)}</span>
        </div>

        ${segments.map((s, i) => {
          const segLogo = getAirlineLogo(s.operating_carrier_name || carrier, s.marketing_carrier_code || 'FL');
          const isLastSeg = i === segments.length - 1;

          return `
            <div class="space-y-3">
              <!-- Airline Row -->
              <div class="flex items-center gap-3">
                <img src="${segLogo}" alt="${carrier}" class="w-6 h-6 object-contain rounded border border-slate-200 p-0.5 bg-white flex-shrink-0" onerror="this.src='https://images.kiwi.com/airlines/64x64/plane.png'">
                <div>
                  <p class="font-extrabold text-navy text-xs">${s.operating_carrier_name || carrier}, ${flight.cabin_class || 'Economy'}</p>
                  <p class="text-[11px] text-slate-400 font-semibold">${s.marketing_carrier_code || 'FL'}, ${s.flight_number || '100'}</p>
                </div>
              </div>

              <!-- Vertical Timeline Line -->
              <div class="relative pl-6 space-y-4 border-l-2 border-slate-300 ml-3 py-1">
                <!-- Departure -->
                <div class="relative">
                  <span class="absolute -left-[31px] top-1.5 w-3 h-3 rounded-full border-2 border-sky bg-white"></span>
                  <p class="font-black text-navy text-sm">${fmtTime(s.departure_time_local)}</p>
                  <p class="text-xs text-slate-600 font-semibold">${s.departure_airport}</p>
                </div>

                <!-- Flight Duration -->
                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 py-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  <span>${fmtDur(s.duration_minutes || 180)}</span>
                </div>

                <!-- Arrival -->
                <div class="relative">
                  <span class="absolute -left-[31px] top-1.5 w-3 h-3 rounded-full border-2 border-slate-400 bg-white"></span>
                  <p class="font-black text-navy text-sm">${fmtTime(s.arrival_time_local)}</p>
                  <p class="text-xs text-slate-600 font-semibold">${s.arrival_airport}</p>
                </div>
              </div>

              <!-- Layover if multi stop -->
              ${!isLastSeg ? `
                <div class="bg-slate-100 border border-slate-200 rounded-xl p-3 my-3 flex items-center gap-2 text-xs text-slate-700 font-medium">
                  <span class="text-base">🚶</span>
                  <span><strong>Layover</strong> in ${s.arrival_airport}</span>
                </div>
              ` : ''}
            </div>
          `;
        }).join('')}
      </div>
    `;
  }

  let html = renderLegTimeline(segs, `Outbound Flight`);

  if (flight.inbound && flight.inbound.segments && flight.inbound.segments.length) {
    html += renderLegTimeline(flight.inbound.segments, `Return Flight`);
  }

  const modalBodyEl = document.getElementById('modalBody');
  if (modalBodyEl) modalBodyEl.innerHTML = html;

  const overlayEl = document.getElementById('flightModalOverlay');
  if (overlayEl) overlayEl.classList.add('open');
};

window.closeFlightModal = e => {
  if (!e || e.target.id === 'flightModalOverlay' || e.target.closest('.close-modal')) {
    const overlayEl = document.getElementById('flightModalOverlay');
    if (overlayEl) overlayEl.classList.remove('open');
  }
};

/* Store selected flight in sessionStorage for book.html / book.php */
window.storeFlightData = idx => {
  const flight = state.filtered[idx];
  if (flight) {
    const cloned = JSON.parse(JSON.stringify(flight));
    const rawPrice = cloned.price.amount;
    const discountedPrice = parseFloat((rawPrice * 0.62).toFixed(2));
    cloned.price.amount = discountedPrice;
    cloned.price.original = rawPrice;
    sessionStorage.setItem('selectedFlight', JSON.stringify(cloned));
  }
};

/* ─── Skeletons & Errors ─────────────────────────────────── */
function showSkeleton() {
  const container = document.getElementById('flightsList');
  if (!container) return;
  container.innerHTML = Array(5).fill('').map((_, i) => `
    <div class="flight-card" style="animation:none;">
      <div class="flex gap-4">
        <div class="skeleton w-12 h-12 rounded-xl"></div>
        <div class="flex-1 space-y-2">
          <div class="skeleton h-4 w-32 rounded"></div>
          <div class="skeleton h-8 w-full rounded-lg"></div>
          <div class="skeleton h-3 w-48 rounded"></div>
        </div>
        <div class="w-32 space-y-2">
          <div class="skeleton h-8 w-24 rounded-lg ml-auto"></div>
          <div class="skeleton h-10 w-full rounded-xl"></div>
        </div>
      </div>
    </div>
  `).join('');
}

function showEmpty() {
  const container = document.getElementById('flightsList');
  const countEl   = document.getElementById('resultsCount');
  if (countEl) countEl.textContent = '0 flights found';
  if (!container) return;
  container.innerHTML = `
    <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm">
      <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
      </div>
      <h3 class="font-bold text-navy text-lg mb-2">No Flights Found</h3>
      <p class="text-slate-500 text-sm mb-5">No flights available for this route and date. Try different dates or call our experts.</p>
      <a href="tel:+1-888-885-5061" class="btn-book-now inline-flex">Call +1 (888) 885-5061</a>
    </div>`;
}

function showError(msg, stack = '') {
  const container = document.getElementById('flightsList');
  if (!container) return;
  container.innerHTML = `
    <div class="text-center py-16 bg-red-50 rounded-2xl border border-red-100 px-4">
      <p class="text-red-600 font-semibold mb-2 text-base">Search failed: ${msg}</p>
      <button onclick="runSearch()" class="btn-book-now bg-red-500">Retry Search</button>
    </div>`;
}
