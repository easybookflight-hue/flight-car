<?php
$pageTitle = 'Details & Payment | Flight Booking';
require_once __DIR__ . '/includes/header.php';
$ignav_id = htmlspecialchars($_GET['ignav_id'] ?? '');
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <!-- Navigation Top -->
  <div class="flex items-center justify-between mb-4">
    <a href="javascript:history.back()" class="inline-flex items-center gap-1.5 text-sky-600 hover:text-sky-700 text-sm font-semibold transition-colors">
      &laquo; Go Back
    </a>
    
    <!-- Stepper Progress Bar (Matching Screenshots) -->
    <div class="flex items-center gap-3 text-xs md:text-sm font-medium">
      <div class="flex items-center gap-1.5 text-emerald-600 font-bold">
        <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs">✓</span>
        <span>Search</span>
      </div>
      <div class="w-10 md:w-16 h-0.5 bg-slate-300"></div>
      <div class="flex items-center gap-1.5 text-sky-600 font-bold">
        <span class="w-5 h-5 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs">2</span>
        <span>Details & Payment</span>
      </div>
      <div class="w-10 md:w-16 h-0.5 bg-slate-200"></div>
      <div class="flex items-center gap-1.5 text-slate-400">
        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-xs">3</span>
        <span>Confirmation</span>
      </div>
    </div>
  </div>

  <!-- Main Grid Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">

    <!-- LEFT COLUMN: Booking Form & Itinerary -->
    <div>
      
      <!-- 1. Flight Itinerary Box -->
      <div class="booking-card" id="itineraryCard">
        <div class="booking-card-header flex justify-between items-center">
          <span>Flight Itinerary</span>
        </div>
        <div class="booking-card-body space-y-5" id="itineraryBody">
          <div class="skeleton h-16 w-full rounded"></div>
        </div>
      </div>

      <form id="bookingForm" onsubmit="event.preventDefault(); handleConfirmAndBook();">
      <!-- 2. Contact Information Box -->
      <div class="booking-card">
        <div class="booking-card-header">Contact Information</div>
        <div class="booking-card-body">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="booking-label">Billing Phone*</label>
              <input type="tel" id="billingPhone" placeholder="Phone Number*" class="booking-input" required>
            </div>
            <div>
              <label class="booking-label">Contact Phone</label>
              <input type="tel" id="contactPhone" placeholder="Alternate Number" class="booking-input">
            </div>
            <div>
              <label class="booking-label">Email*</label>
              <input type="email" id="billingEmail" placeholder="Your Email" class="booking-input" required>
            </div>
            <div>
              <label class="booking-label">Retype Email*</label>
              <input type="email" id="retypeEmail" placeholder="Retype Email" class="booking-input" required>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Passenger's Name Box -->
      <div class="booking-card">
        <div class="booking-card-header">Passenger's Name</div>
        <div class="booking-card-body">
          <div class="bg-sky-50 border border-sky-200 rounded-lg p-3 text-xs text-sky-900 mb-4 flex items-start gap-2">
            <svg class="w-4 h-4 text-sky-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><strong>Important :</strong> Please enter the traveler's name and date of birth exactly as shown on the passport (for international flights) or valid government-issued photo ID (for domestic flights) to be used on this trip.</span>
          </div>

          <h4 class="font-bold text-slate-800 text-sm mb-3" id="paxHeader">Passenger : 1 Adult</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <div>
              <label class="booking-label">First Name*</label>
              <input type="text" placeholder="First Name*" class="booking-input" required>
            </div>
            <div>
              <label class="booking-label">Middle Name</label>
              <input type="text" placeholder="Middle Name" class="booking-input">
            </div>
            <div>
              <label class="booking-label">Last Name*</label>
              <input type="text" placeholder="Last Name*" class="booking-input" required>
            </div>
            <div>
              <label class="booking-label">Gender*</label>
              <select class="booking-input" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>

          <div>
            <label class="booking-label">Date Of Birth*</label>
            <div class="grid grid-cols-3 gap-3 max-w-xs">
              <select class="booking-input" required id="dobDay">
                <option value="">Day</option>
                <?php for($d=1;$d<=31;$d++): ?>
                <option value="<?= sprintf('%02d',$d) ?>"><?= sprintf('%02d',$d) ?></option>
                <?php endfor; ?>
              </select>
              <select class="booking-input" required id="dobMonth">
                <option value="">Month</option>
                <?php foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $idx => $m): ?>
                <option value="<?= sprintf('%02d',$idx+1) ?>"><?= $m ?></option>
                <?php endforeach; ?>
              </select>
              <select class="booking-input" required id="dobYear">
                <option value="">Year</option>
                <?php for($y=2026;$y>=1940;$y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Payment Information Box -->
      <div class="booking-card">
        <div class="booking-card-header">Payment Information</div>
        <div class="booking-card-body">
          <div class="bg-emerald-50 text-emerald-700 text-xs font-bold p-2.5 rounded-md mb-4 flex items-center gap-1.5 border border-emerald-200">
            <span>🔒 SAFE AND SECURE BILLING THIS IS A SECURE 256-BIT SSL ENCRYPTED PAYMENT . YOU ARE SAFE!</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="booking-label">Card Number*</label>
              <div class="relative">
                <input type="text" placeholder="Card Number*" class="booking-input pr-28" required>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                  <span class="text-[10px] font-bold bg-blue-700 text-white px-1.5 py-0.5 rounded">VISA</span>
                  <span class="text-[10px] font-bold bg-red-600 text-white px-1.5 py-0.5 rounded">MC</span>
                  <span class="text-[10px] font-bold bg-sky-600 text-white px-1.5 py-0.5 rounded">AMEX</span>
                </div>
              </div>
            </div>

            <div>
              <label class="booking-label">Expiration Date*</label>
              <div class="grid grid-cols-2 gap-2">
                <select class="booking-input" required>
                  <option value="">Month</option>
                  <?php for($m=1;$m<=12;$m++): ?>
                  <option value="<?= sprintf('%02d',$m) ?>"><?= sprintf('%02d',$m) ?></option>
                  <?php endfor; ?>
                </select>
                <select class="booking-input" required>
                  <option value="">Year</option>
                  <?php for($y=2026;$y<=2040;$y++): ?>
                  <option value="<?= $y ?>"><?= $y ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>

            <div>
              <label class="booking-label">Security Code (CVV)*</label>
              <input type="text" placeholder="CVV No" class="booking-input" max="4" required>
            </div>

            <div class="md:col-span-2">
              <label class="booking-label">Full Name on Card*</label>
              <input type="text" placeholder="Card Holder's Full name" class="booking-input" required>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Billing Information Box -->
      <div class="booking-card">
        <div class="booking-card-header">Billing Information</div>
        <div class="booking-card-body">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="booking-label">Country*</label>
              <select class="booking-input" required>
                <option value="United States">United States</option>
                <option value="India">India</option>
                <option value="Canada">Canada</option>
                <option value="United Kingdom">United Kingdom</option>
              </select>
            </div>
            <div>
              <label class="booking-label">State*</label>
              <select class="booking-input" required>
                <option value="">Select State</option>
                <option value="NY">New York</option>
                <option value="CA">California</option>
                <option value="TX">Texas</option>
                <option value="FL">Florida</option>
                <option value="DL">Delhi</option>
                <option value="MH">Maharashtra</option>
              </select>
            </div>
            <div>
              <label class="booking-label">Address 1*</label>
              <input type="text" placeholder="Address Line 1*" class="booking-input" required>
            </div>
            <div>
              <label class="booking-label">Address 2</label>
              <input type="text" placeholder="Address Line 2" class="booking-input">
            </div>
            <div>
              <label class="booking-label">City*</label>
              <input type="text" placeholder="City*" class="booking-input" required>
            </div>
            <div>
              <label class="booking-label">Postal/Zip Code*</label>
              <input type="text" placeholder="Postal/Zip Code*" class="booking-input" required>
            </div>
          </div>
        </div>
      </div>

      <!-- 6. Policies And Review Box -->
      <div class="booking-card">
        <div class="booking-card-header">Policies And Review</div>
        <div class="booking-card-body text-xs text-slate-600 space-y-3">
          <p class="font-semibold text-slate-800">Passenger names must match passport or government issued photo ID exactly. Name changes are not allowed.</p>
          <label class="flex items-start gap-2.5 cursor-pointer">
            <input type="checkbox" checked required class="mt-0.5 rounded border-slate-300 text-sky-600">
            <span>Please confirm that the names of travelers, date and time of flight departures are accurate. Tickets are non-transferable and any change in the name of the traveler is not permitted. Date and routing changes will be subject to airline penalties and service fees; fares are not guaranteed until ticketed. All our service fees and tax are included in the total ticket cost. You may receive a call or email in case there is an issue with payment authentication or booking failure.</span>
          </label>
        </div>
      </div>

      <!-- 7. Action Button & Security note -->
      <div class="text-center py-4">
        <button type="submit" class="btn-confirm-booking">
          Confirm & Book
          <span class="text-[11px] bg-black/20 px-2 py-0.5 rounded font-normal">SECURE PAYMENT</span>
        </button>
        <div class="flex items-center justify-center gap-2 text-xs text-slate-500 max-w-md mx-auto mt-4 text-center">
          <svg class="w-5 h-5 text-sky-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span>Your payment and personal details are kept highly secure and strictly confidential, safeguarded with advanced encryption technology to ensure your privacy and protection at every step of the process.</span>
        </div>
      </div>
      </form>

    </div>

    <!-- RIGHT COLUMN: Sticky Sidebar -->
    <div class="space-y-4">
      
      <!-- Session timer -->
      <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm text-xs text-slate-600">
        Please make your payment within the next <strong id="timerCountdown" class="text-sky-600 text-sm font-extrabold">11:50</strong> to keep the session active.
      </div>

      <!-- Price Details (USD) -->
      <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 p-4">
          <h3 class="font-bold text-navy text-base">Price Details (USD)</h3>
        </div>
        <div class="p-4 space-y-3 text-xs" id="priceBreakdown">
          <div class="flex justify-between font-bold text-slate-400 border-b pb-2">
            <span>Travelers</span>
            <span>Subtotal</span>
          </div>
          <div class="flex justify-between text-slate-700" id="pricePaxRow">
            <span>1 Adult X USD$<span id="baseAmt">196.28</span></span>
            <span>USD$<span id="baseSubtotal">196.28</span></span>
          </div>
          <div class="flex justify-between text-slate-700">
            <span>Taxes & Fees</span>
            <span>USD$<span id="taxAmt">45.50</span></span>
          </div>
          <div class="flex justify-between font-extrabold text-sm text-navy border-t pt-3 border-slate-200">
            <span>Total Price (USD):</span>
            <span class="text-base text-sky-600">USD$<span id="totalAmt">241.78</span></span>
          </div>
          <p class="text-[11px] text-slate-400 mt-2 leading-relaxed">
            All fares are quoted in USD. Some airlines may charge baggage fees. Your credit/debit card may be billed in multiple charges totaling the final total price.
          </p>
        </div>
      </div>

      <!-- Why book with us? -->
      <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm space-y-2">
        <h4 class="font-bold text-navy text-sm mb-2">Why book with us?</h4>
        <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
          <span class="text-emerald-500 font-bold">✓</span>
          <span>Exclusive discounts with all major airlines</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
          <span class="text-emerald-500 font-bold">✓</span>
          <span>Fast, easy, and secure</span>
        </div>
      </div>

      <!-- Online Support -->
      <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex items-start gap-3">
        <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        </div>
        <div>
          <h4 class="font-bold text-navy text-sm">Online Support</h4>
          <p class="text-xs text-slate-500">Need help? Call us toll-free</p>
          <a href="tel:<?= SUPPORT_PHONE ?>" class="font-bold text-sky-600 text-sm hover:underline block mt-0.5"><?= SUPPORT_PHONE ?></a>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal for Ignav Real-time Booking Links -->
<div id="bookingLinksModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative animate-fade-up">
    <button onclick="closeBookingModal()" class="absolute right-4 top-4 text-slate-400 hover:text-navy text-xl font-bold">&times;</button>
    
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      </div>
      <div>
        <h3 class="font-extrabold text-navy text-lg">Select Official Booking Partner</h3>
        <p class="text-xs text-slate-500">Real-time direct booking options powered by Ignav API</p>
      </div>
    </div>

    <!-- Modal Flight Summary -->
    <div id="modalFlightSummary" class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 mb-4 text-xs"></div>

    <!-- Provider Links -->
    <div id="modalProvidersList" class="space-y-3">
      <div class="skeleton h-12 w-full rounded-xl"></div>
      <div class="skeleton h-12 w-full rounded-xl"></div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/main.js?v=1.2"></script>

<script>
const IGNAV_ID = '<?= $ignav_id ?>';

function getLogo(carrier, iata) {
  if (iata) return `https://pics.avs.io/200/60/${iata.toUpperCase()}.png`;
  return `https://images.kiwi.com/airlines/64x64/plane.png`;
}

function fmtDateStr(dtStr) {
  if (!dtStr) return '';
  const d = new Date(dtStr);
  if (isNaN(d)) return dtStr;
  return d.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
}

function fmtTimeStr(dtStr) {
  if (!dtStr) return '';
  const d = new Date(dtStr);
  if (isNaN(d)) return dtStr;
  return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

function fmtMinutes(m) {
  if (!m) return '';
  const hrs = Math.floor(m / 60);
  const mins = m % 60;
  return `${hrs} hr ${mins} min`;
}

document.addEventListener('DOMContentLoaded', () => {
  startTimer(710);
  const stored = sessionStorage.getItem('selectedFlight');
  if (stored) {
    try {
      const flight = JSON.parse(stored);
      renderItinerary(flight);
      renderPriceBreakdown(flight);
    } catch (e) {}
  }
});

function renderItinerary(f) {
  const segs = f.outbound?.segments || [];
  if (!segs.length) return;

  const first = segs[0];
  const last  = segs[segs.length - 1];
  const carrier = f.outbound.carrier || first.operating_carrier_name;
  const iata = first.marketing_carrier_code || 'B6';
  const logo = getLogo(carrier, iata);
  const cabinLabel = (f.cabin_class || 'economy').replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());

  let html = `
    <div class="border-b border-slate-100 pb-4">
      <div class="flex items-center justify-between mb-3">
        <span class="font-bold text-sky-700 text-sm flex items-center gap-1.5">
          <span>✈</span> Depart, ${fmtDateStr(first.departure_time_local)}
        </span>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-[160px_1fr_1fr] items-center gap-4 bg-slate-50 p-3.5 rounded-lg border border-slate-200">
        <div class="flex items-center gap-3">
          <img src="${logo}" alt="${carrier}" class="h-8 w-auto max-w-[90px] object-contain" onerror="this.onerror=null;this.src='https://images.kiwi.com/airlines/64x64/${iata}.png'">
          <div>
            <p class="font-bold text-navy text-xs">${carrier}</p>
            <p class="text-[11px] text-slate-500 font-semibold">Flight Number : <strong>${iata} ${first.flight_number}</strong></p>
            <p class="text-[11px] text-slate-400">Class: <strong>${cabinLabel}</strong></p>
          </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
          <div>
            <p class="font-extrabold text-navy text-sm">${fmtTimeStr(first.departure_time_local)}</p>
            <p class="text-slate-600 font-medium">${first.departure_airport}</p>
            <p class="text-slate-400 text-[11px]">${fmtDateStr(first.departure_time_local)}</p>
          </div>
          <div class="flex-1 text-center text-[11px] text-slate-400">
            <span class="block mb-0.5">⏱ ${fmtMinutes(f.outbound.duration_minutes)}</span>
            <div class="h-0.5 bg-slate-300 w-full rounded"></div>
          </div>
          <div>
            <p class="font-extrabold text-navy text-sm">${fmtTimeStr(last.arrival_time_local)}</p>
            <p class="text-slate-600 font-medium">${last.arrival_airport}</p>
            <p class="text-slate-400 text-[11px]">${fmtDateStr(last.arrival_time_local)}</p>
          </div>
        </div>
      </div>
    </div>
  `;

  if (f.inbound && f.inbound.segments && f.inbound.segments.length) {
    const inSegs = f.inbound.segments;
    const inFirst = inSegs[0];
    const inLast  = inSegs[inSegs.length - 1];
    const inCarrier = f.inbound.carrier || inFirst.operating_carrier_name;
    const inIata = inFirst.marketing_carrier_code || 'B6';
    const inLogo = getLogo(inCarrier, inIata);

    html += `
      <div class="pt-2">
        <div class="flex items-center justify-between mb-3">
          <span class="font-bold text-sky-700 text-sm flex items-center gap-1.5">
            <span>✈</span> Return, ${fmtDateStr(inFirst.departure_time_local)}
          </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-[160px_1fr_1fr] items-center gap-4 bg-slate-50 p-3.5 rounded-lg border border-slate-200">
          <div class="flex items-center gap-3">
            <img src="${inLogo}" alt="${inCarrier}" class="h-8 w-auto max-w-[90px] object-contain" onerror="this.onerror=null;this.src='https://images.kiwi.com/airlines/64x64/${inIata}.png'">
            <div>
              <p class="font-bold text-navy text-xs">${inCarrier}</p>
              <p class="text-[11px] text-slate-500 font-semibold">Flight Number : <strong>${inIata} ${inFirst.flight_number}</strong></p>
              <p class="text-[11px] text-slate-400">Class: <strong>${cabinLabel}</strong></p>
            </div>
          </div>

          <div class="flex items-center gap-4 text-xs">
            <div>
              <p class="font-extrabold text-navy text-sm">${fmtTimeStr(inFirst.departure_time_local)}</p>
              <p class="text-slate-600 font-medium">${inFirst.departure_airport}</p>
              <p class="text-slate-400 text-[11px]">${fmtDateStr(inFirst.departure_time_local)}</p>
            </div>
            <div class="flex-1 text-center text-[11px] text-slate-400">
              <span class="block mb-0.5">⏱ ${fmtMinutes(f.inbound.duration_minutes)}</span>
              <div class="h-0.5 bg-slate-300 w-full rounded"></div>
            </div>
            <div>
              <p class="font-extrabold text-navy text-sm">${fmtTimeStr(inLast.arrival_time_local)}</p>
              <p class="text-slate-600 font-medium">${inLast.arrival_airport}</p>
              <p class="text-slate-400 text-[11px]">${fmtDateStr(inLast.arrival_time_local)}</p>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  document.getElementById('itineraryBody').innerHTML = html;
}

function renderPriceBreakdown(f) {
  const total = f.price?.amount || 241.78;
  const base  = (total * 0.81).toFixed(2);
  const tax   = (total * 0.19).toFixed(2);
  const totStr = total.toFixed(2);

  document.getElementById('baseAmt').textContent = base;
  document.getElementById('baseSubtotal').textContent = base;
  document.getElementById('taxAmt').textContent = tax;
  document.getElementById('totalAmt').textContent = totStr;
}

function startTimer(durationSeconds) {
  let timer = durationSeconds;
  const display = document.getElementById('timerCountdown');
  if (!display) return;

  const interval = setInterval(() => {
    const minutes = Math.floor(timer / 60);
    const seconds = timer % 60;
    display.textContent = `${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
    if (--timer < 0) {
      clearInterval(interval);
      display.textContent = "00:00";
    }
  }, 1000);
}

async function handleConfirmAndBook() {
  const modal = document.getElementById('bookingLinksModal');
  const providersContainer = document.getElementById('modalProvidersList');
  const summaryContainer = document.getElementById('modalFlightSummary');
  modal.classList.remove('hidden');
  modal.classList.add('flex');

  const stored = sessionStorage.getItem('selectedFlight');
  const f = stored ? JSON.parse(stored) : null;
  
  if (f) {
    const carrier = f.outbound?.carrier || 'Airline';
    const first = f.outbound?.segments?.[0];
    const last  = f.outbound?.segments?.[f.outbound.segments.length - 1];
    summaryContainer.innerHTML = `
      <div class="flex items-center justify-between">
        <div>
          <p class="font-bold text-navy text-sm">${carrier} (${first?.departure_airport} → ${last?.arrival_airport})</p>
          <p class="text-[11px] text-slate-500">${fmtDateStr(first?.departure_time_local)}</p>
        </div>
        <p class="text-base font-extrabold text-emerald-600">$${f.price?.amount.toFixed(2)} USD</p>
      </div>
    `;
  }

  const targetId = IGNAV_ID || f?.ignav_id;
  if (!targetId) {
    providersContainer.innerHTML = `
      <div class="bg-amber-50 p-4 rounded-xl text-xs text-amber-800">
        <p class="font-bold mb-1">Direct booking links available via call</p>
        <p>Please call our helpline to confirm your ticket: <a href="tel:<?= SUPPORT_PHONE ?>" class="font-bold underline"><?= SUPPORT_PHONE ?></a></p>
      </div>`;
    return;
  }

  try {
    let res;
    try {
      res = await fetch('api/booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ignav_id: targetId }),
      });
    } catch(e) { res = null; }

    if (!res || !res.ok) {
      res = await fetch(`api/booking.php?ignav_id=${encodeURIComponent(targetId)}`);
    }

    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    const options = data.booking_options || [];
    const allLinks = options.flatMap(o => o.links || []);

    if (!allLinks.length) {
      providersContainer.innerHTML = `
        <div class="bg-amber-50 p-4 rounded-xl text-xs text-amber-800">
          <p class="font-bold mb-1">No direct links returned for this fare</p>
          <p>Call expert agent now: <a href="tel:<?= SUPPORT_PHONE ?>" class="font-bold underline"><?= SUPPORT_PHONE ?></a></p>
        </div>`;
      return;
    }

    providersContainer.innerHTML = allLinks.map((l, i) => `
      <div class="border border-slate-200 rounded-xl p-3.5 flex items-center justify-between bg-slate-50 hover:bg-white hover:border-emerald-500 transition-all shadow-sm">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="font-bold text-navy text-sm">${l.provider_name}</span>
            ${l.provider_type === 'airline' ? '<span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold">Official Airline</span>' : '<span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded">OTA Partner</span>'}
          </div>
          <p class="text-[11px] text-slate-500">Live price confirmed via Ignav</p>
        </div>
        <a href="${l.url}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
          Continue to Book &rarr;
        </a>
      </div>
    `).join('');

  } catch (err) {
    providersContainer.innerHTML = `
      <div class="bg-amber-50 p-4 rounded-xl text-xs text-amber-800">
        <p class="font-bold mb-1">Could not fetch partner links</p>
        <p>Call toll-free for manual booking: <a href="tel:<?= SUPPORT_PHONE ?>" class="font-bold underline"><?= SUPPORT_PHONE ?></a></p>
      </div>`;
  }
}

function closeBookingModal() {
  const modal = document.getElementById('bookingLinksModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
</script>
