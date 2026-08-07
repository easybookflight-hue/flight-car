// ── SKYFARE / CHEAP FLIGHTS US SEARCH & LEADS CONTROLLER ──
// Data flow: Website → api/save_search.php → Google Sheet + CRM DB

let ALL_AIRPORTS_DATA = [];

document.addEventListener('DOMContentLoaded', () => {
  const formIds = ['searchForm', 'flightSearchForm'];
  formIds.forEach(id => {
    const form = document.getElementById(id);
    if (form) {
      form.addEventListener('submit', handleSearchSubmit);
    }
  });

  initDefaultDates();
  loadAllAirportsData();
  setupAirportAutocomplete('originInput', 'originDropdown', 'originCode');
  setupAirportAutocomplete('destInput', 'destDropdown', 'destCode');
});

// ── DEFAULT DEPART DATE (Next Month / +30 Days) ─────────────
function initDefaultDates() {
  const depInput = document.getElementById('depDate');
  if (depInput && !depInput.value) {
    const today = new Date();
    const nextMonth = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);
    const yyyy = nextMonth.getFullYear();
    const mm   = String(nextMonth.getMonth() + 1).padStart(2, '0');
    const dd   = String(nextMonth.getDate()).padStart(2, '0');
    depInput.value = `${yyyy}-${mm}-${dd}`;
    depInput.min   = today.toISOString().split('T')[0];
  }
}

// ── TRAVELERS & CABIN CONTROLLER ────────────────────────────
window.changePax = function(type, delta) {
  const inputEl = document.getElementById(type + 'Input');
  const countEl = document.getElementById(type + 'Count');

  let current = parseInt(inputEl ? inputEl.value : (countEl ? countEl.textContent : '0'), 10) || 0;
  let min = (type === 'adults') ? 1 : 0;
  let updated = Math.max(min, current + delta);

  if (inputEl) inputEl.value = updated;
  if (countEl) countEl.textContent = updated;

  updateTravelersLabel();
};

window.updateTravelersLabel = function() {
  const adults   = parseInt(document.getElementById('adultsInput')?.value || document.getElementById('adultsCount')?.textContent || '1', 10) || 1;
  const children = parseInt(document.getElementById('childrenInput')?.value || document.getElementById('childrenCount')?.textContent || '0', 10) || 0;
  const infants  = parseInt(document.getElementById('infantsInput')?.value || document.getElementById('infantsCount')?.textContent || '0', 10) || 0;

  const total = adults + children + infants;
  const cabinSelect = document.getElementById('cabinClass') || document.getElementById('cabinSelect');
  let cabinText = 'Economy';
  if (cabinSelect) {
    cabinText = cabinSelect.options[cabinSelect.selectedIndex]?.text || cabinSelect.value || 'Economy';
  }

  const paxBtn = document.getElementById('paxBtn');
  if (paxBtn) {
    paxBtn.textContent = `${total} Traveler${total > 1 ? 's' : ''} · ${cabinText}`;
  }
};

window.doSearch = function(e) {
  return handleSearchSubmit(e);
};

function handleSearchSubmit(e) {
  if (e && e.preventDefault) e.preventDefault();

  const originInput = document.getElementById('originInput')?.value || '';
  const originCode  = document.getElementById('originCode')?.value || '';
  const origin      = (originCode || originInput).trim();

  const destInput   = document.getElementById('destInput')?.value || '';
  const destCode    = document.getElementById('destCode')?.value || '';
  const dest        = (destCode || destInput).trim();

  const dep  = document.getElementById('depDate')?.value || document.getElementById('depDateInput')?.value || '';
  const ret  = document.getElementById('retDate')?.value || document.getElementById('retDateInput')?.value || '';
  const trip = document.getElementById('tripType')?.value || document.getElementById('tripTypeSelect')?.value || 'one-way';

  const adults   = parseInt(document.getElementById('adultsInput')?.value || '1', 10);
  const children = parseInt(document.getElementById('childrenInput')?.value || '0', 10);
  const infants  = parseInt(document.getElementById('infantsInput')?.value || '0', 10);
  const pax      = (adults + children + infants) || document.getElementById('paxSelect')?.value || '1';

  const cabin = document.getElementById('cabinClass')?.value || document.getElementById('cabinSelect')?.value || 'Economy';
  const name  = document.getElementById('contactName')?.value || document.getElementById('searchCustName')?.value || 'Traveler';

  const phoneNum = document.getElementById('contactPhone')?.value || document.getElementById('searchCustPhone')?.value || '';
  const cCode    = document.getElementById('countryCode')?.value || '';
  const phone    = phoneNum ? (cCode + ' ' + phoneNum).trim() : '';

  if (!origin || !dest || !dep) {
    alert('Please fill in Origin, Destination, and Departure Date.');
    return false;
  }

  const rawDigits = phoneNum.replace(/\D/g, '');
  if (!phoneNum || rawDigits.length < 7) {
    alert('⚠️ Phone Number is required! Please enter a valid phone number before searching.');
    const phoneInputEl = document.getElementById('contactPhone') || document.getElementById('searchCustPhone');
    if (phoneInputEl) {
      phoneInputEl.focus();
      phoneInputEl.style.borderColor = '#ef4444';
    }
    return false;
  }

  const cleanPhone = phone ? (phone.startsWith('+') ? "'" + phone : phone) : '';

  const payload = {
    action: 'search',
    customer_name: name,
    phone: cleanPhone,
    origin: origin,
    destination: dest,
    dep_date: dep,
    return_date: ret,
    trip_type: trip,
    passengers: pax,
    cabin: cabin,
    created_at: new Date().toISOString()
  };

  const GOOGLE_SHEET_URL = 'https://script.google.com/macros/s/AKfycby8tLEKYjEZ5taY5o67Y4ZggDahzbQx3Bwb_zgNHxaiIIQq7I65mJaUs1AM5_Jb8fJaTw/exec';

  // 1. Send to PHP/Python API
  try {
    const apiBase = location.port === '5500' ? 'http://localhost:8090/api/save_search.php' : 'api/save_search.php';
    fetch(apiBase, {
      method: 'POST',
      keepalive: true,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).catch(() => {});
  } catch(err) {}

  // 2. Direct client-side Google Sheet submission (guarantees delivery on Live Server 5500)
  try {
    fetch(GOOGLE_SHEET_URL, {
      method: 'POST',
      mode: 'no-cors',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).catch(() => {});
  } catch(err) {}

  // Redirect to results page
  const queryParams = new URLSearchParams({
    from: origin,
    to: dest,
    dep: dep,
    ret: ret,
    trip: trip,
    pax: pax,
    cabin: cabin
  });
  window.location.href = 'results.html?' + queryParams.toString();
  return false;
}

async function loadAllAirportsData() {
  try {
    const dataPath = location.pathname.includes('/crm/') ? '../assets/data/airports.json' : 'assets/data/airports.json';
    const res = await fetch(dataPath);
    if (res.ok) {
      ALL_AIRPORTS_DATA = await res.json();
    }
  } catch (err) {}
}

// ── AIRPORT AUTOCOMPLETE CONTROLLER ──
const POPULAR_AIRPORTS = [
  { code: 'JFK', name: 'John F. Kennedy Intl', city: 'New York', country: 'United States' },
  { code: 'LHR', name: 'Heathrow Airport', city: 'London', country: 'United Kingdom' },
  { code: 'LAX', name: 'Los Angeles Intl', city: 'Los Angeles', country: 'United States' },
  { code: 'DXB', name: 'Dubai International', city: 'Dubai', country: 'United Arab Emirates' },
  { code: 'CDG', name: 'Charles de Gaulle', city: 'Paris', country: 'France' },
  { code: 'DEL', name: 'Indira Gandhi Intl', city: 'New Delhi', country: 'India' },
  { code: 'BOM', name: 'Chhatrapati Shivaji Maharaj', city: 'Mumbai', country: 'India' },
  { code: 'ORD', name: 'O\'Hare International', city: 'Chicago', country: 'United States' },
  { code: 'SFO', name: 'San Francisco Intl', city: 'San Francisco', country: 'United States' },
  { code: 'MIA', name: 'Miami International', city: 'Miami', country: 'United States' },
  { code: 'YYZ', name: 'Toronto Pearson Intl', city: 'Toronto', country: 'Canada' },
  { code: 'SIN', name: 'Changi Airport', city: 'Singapore', country: 'Singapore' },
  { code: 'HND', name: 'Haneda Airport', city: 'Tokyo', country: 'Japan' },
  { code: 'NRT', name: 'Narita International', city: 'Tokyo', country: 'Japan' },
  { code: 'SYD', name: 'Sydney Airport', city: 'Sydney', country: 'Australia' },
  { code: 'FRA', name: 'Frankfurt Airport', city: 'Frankfurt', country: 'Germany' },
  { code: 'AMS', name: 'Schiphol Airport', city: 'Amsterdam', country: 'Netherlands' },
  { code: 'BKK', name: 'Suvarnabhumi Airport', city: 'Bangkok', country: 'Thailand' },
  { code: 'ICN', name: 'Incheon International', city: 'Seoul', country: 'South Korea' },
  { code: 'HKG', name: 'Hong Kong International', city: 'Hong Kong', country: 'Hong Kong' },
  { code: 'DOH', name: 'Hamad International', city: 'Doha', country: 'Qatar' },
  { code: 'MAA', name: 'Chennai International', city: 'Chennai', country: 'India' },
  { code: 'BLR', name: 'Kempegowda International', city: 'Bengaluru', country: 'India' },
  { code: 'HYD', name: 'Rajiv Gandhi Intl', city: 'Hyderabad', country: 'India' },
  { code: 'CCU', name: 'Netaji Subhash Chandra Bose', city: 'Kolkata', country: 'India' },
  { code: 'MUC', name: 'Munich Airport', city: 'Munich', country: 'Germany' },
  { code: 'BCN', name: 'Barcelona-El Prat', city: 'Barcelona', country: 'Spain' },
  { code: 'MAD', name: 'Madrid–Barajas', city: 'Madrid', country: 'Spain' },
  { code: 'ATL', name: 'Hartsfield-Jackson Atlanta', city: 'Atlanta', country: 'United States' },
  { code: 'EWR', name: 'Newark Liberty Intl', city: 'Newark', country: 'United States' },
  { code: 'LGA', name: 'LaGuardia Airport', city: 'New York', country: 'United States' },
  { code: 'IAH', name: 'George Bush Intercontinental', city: 'Houston', country: 'United States' },
  { code: 'MCO', name: 'Orlando International', city: 'Orlando', country: 'United States' },
  { code: 'LAS', name: 'Harry Reid International', city: 'Las Vegas', country: 'United States' },
  { code: 'SEA', name: 'Seattle-Tacoma Intl', city: 'Seattle', country: 'United States' }
];

function setupAirportAutocomplete(inputId, dropdownId, hiddenCodeId) {
  const input    = document.getElementById(inputId);
  const dropdown = document.getElementById(dropdownId);
  const hidden   = document.getElementById(hiddenCodeId);

  if (!input || !dropdown) return;

  function renderList(airports) {
    if (!airports || !airports.length) {
      dropdown.classList.remove('open');
      dropdown.innerHTML = '';
      return;
    }
    dropdown.innerHTML = airports.map(a => `
      <div class="autocomplete-item p-2.5 hover:bg-sky-50 cursor-pointer border-b border-slate-100 last:border-0 transition-colors flex items-center justify-between"
           data-code="${a.code}" data-city="${a.city || a.name}" data-name="${a.name}">
        <div>
          <div class="font-bold text-slate-800 text-xs">${a.city || a.name} (${a.code})</div>
          <div class="text-[11px] text-slate-500 truncate max-w-[220px]">${a.name} ${a.country ? '· ' + a.country : ''}</div>
        </div>
        <span class="text-[10px] font-bold bg-slate-100 text-navy px-2 py-0.5 rounded">${a.code}</span>
      </div>
    `).join('');

    dropdown.classList.add('open');

    dropdown.querySelectorAll('.autocomplete-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.stopPropagation();
        const code = item.dataset.code;
        const city = item.dataset.city;
        input.value = `${city} (${code})`;
        if (hidden) hidden.value = code;
        dropdown.classList.remove('open');
      });
    });
  }

  let debounceTimer;
  input.addEventListener('input', (e) => {
    const q = e.target.value.trim().toLowerCase();
    if (!q) {
      dropdown.classList.remove('open');
      dropdown.innerHTML = '';
      if (hidden) hidden.value = '';
      return;
    }

    const pool = (ALL_AIRPORTS_DATA && ALL_AIRPORTS_DATA.length > 0) ? ALL_AIRPORTS_DATA : POPULAR_AIRPORTS;

    // Search: exact code matches first, then prefix, then substring
    const exactMatches = [];
    const prefixMatches = [];
    const substringMatches = [];

    for (const a of pool) {
      if (!a || !a.code) continue;
      const code = (a.code || '').toLowerCase();
      const city = (a.city || '').toLowerCase();
      const name = (a.name || '').toLowerCase();
      const country = (a.country || '').toLowerCase();

      if (code === q) {
        exactMatches.push(a);
      } else if (code.startsWith(q) || city.startsWith(q)) {
        prefixMatches.push(a);
      } else if (code.includes(q) || city.includes(q) || name.includes(q) || country.includes(q)) {
        substringMatches.push(a);
      }

      if (exactMatches.length + prefixMatches.length + substringMatches.length >= 24) {
        break;
      }
    }

    const matches = [...exactMatches, ...prefixMatches, ...substringMatches].slice(0, 12);
    renderList(matches);

    // Fallback: API call if pool is empty
    if (!matches.length) {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(async () => {
        try {
          const apiPath = location.pathname.includes('/crm/') ? '../api/airports.php' : 'api/airports.php';
          const res = await fetch(`${apiPath}?q=${encodeURIComponent(q)}&limit=12`);
          if (res.ok) {
            const apiData = await res.json();
            if (Array.isArray(apiData) && apiData.length > 0) {
              renderList(apiData);
            }
          }
        } catch (err) {}
      }, 200);
    }
  });

  input.addEventListener('focus', () => {
    const q = input.value.trim().toLowerCase();
    if (q.length > 0) {
      input.dispatchEvent(new Event('input'));
    }
  });

  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.remove('open');
    }
  });
}
