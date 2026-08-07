<?php
$pageTitle = 'Car Rental Lead Dashboard & CRM';
require_once __DIR__ . '/includes/header.php';

$csvFile = __DIR__ . '/data/cab_bookings.csv';
$leads = [];

if (file_exists($csvFile)) {
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 10) {
                $leads[] = $data;
            }
        }
        fclose($handle);
    }
}
$leads = array_reverse($leads); // Newest leads first
?>

<section class="py-10 bg-slate-100 min-h-screen text-slate-900">
  <div class="max-w-7xl mx-auto px-4 space-y-6">
    
    <div class="bg-white p-6 rounded-3xl shadow-md border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <span class="bg-emerald-100 text-emerald-800 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider">LIVE CRM LEADS</span>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy mt-1">Car Rental Booking Leads</h1>
        <p class="text-xs text-slate-500 mt-1">All leads submitted via website form &amp; synced with Google Sheets</p>
      </div>

      <div class="flex items-center gap-3">
        <?php if (file_exists($csvFile)): ?>
          <a href="data/cab_bookings.csv" download class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs px-5 py-2.5 rounded-xl shadow transition-all">
            <i data-lucide="download" class="w-4 h-4"></i> Download CSV Excel File
          </a>
        <?php endif; ?>
        <button onclick="window.location.reload()" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-all border border-slate-300">
          <i data-lucide="rotate-cw" class="w-4 h-4"></i>
        </button>
      </div>
    </div>

    <!-- LEADS TABLE -->
    <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-extrabold text-navy text-sm">Total Submitted Leads (<?= count($leads) ?>)</h3>
        <span class="text-xs text-slate-400 font-medium">Real-time Auto Refresh</span>
      </div>

      <?php if (empty($leads)): ?>
        <div class="p-12 text-center text-slate-400 space-y-2">
          <i data-lucide="inbox" class="w-12 h-12 mx-auto text-slate-300"></i>
          <p class="font-bold text-slate-600">No leads submitted yet</p>
          <p class="text-xs">Submit a test booking on <a href="cab-booking.php" class="text-amber-600 underline">cab-booking.php</a> to view data here.</p>
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-800 text-amber-400 uppercase tracking-wider font-extrabold text-[10px]">
              <tr>
                <th class="p-3.5">Time</th>
                <th class="p-3.5">Customer Name</th>
                <th class="p-3.5">Phone</th>
                <th class="p-3.5">Email</th>
                <th class="p-3.5">Vehicle</th>
                <th class="p-3.5">Ride Type</th>
                <th class="p-3.5">Pickup Address</th>
                <th class="p-3.5">Date &amp; Time</th>
                <th class="p-3.5">Fare</th>
                <th class="p-3.5">Flight No</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
              <?php foreach ($leads as $lead): ?>
                <tr class="hover:bg-amber-50/50 transition-colors">
                  <td class="p-3.5 font-bold text-slate-900 whitespace-nowrap"><?= htmlspecialchars($lead[0] ?? '') ?></td>
                  <td class="p-3.5 font-black text-navy text-xs"><?= htmlspecialchars($lead[8] ?? '') ?></td>
                  <td class="p-3.5 text-amber-600 font-extrabold whitespace-nowrap">
                    <a href="tel:<?= htmlspecialchars($lead[9] ?? '') ?>" class="hover:underline flex items-center gap-1">
                      <i data-lucide="phone" class="w-3 h-3"></i> <?= htmlspecialchars($lead[9] ?? '') ?>
                    </a>
                  </td>
                  <td class="p-3.5 text-slate-500"><?= htmlspecialchars($lead[10] ?? '') ?></td>
                  <td class="p-3.5 font-bold"><span class="bg-slate-100 text-navy px-2 py-0.5 rounded border border-slate-200"><?= htmlspecialchars($lead[3] ?? '') ?></span></td>
                  <td class="p-3.5 text-slate-600"><?= htmlspecialchars($lead[1] ?? '') ?></td>
                  <td class="p-3.5 max-w-xs truncate" title="<?= htmlspecialchars($lead[4] ?? '') ?>"><?= htmlspecialchars($lead[4] ?? '') ?></td>
                  <td class="p-3.5 whitespace-nowrap text-slate-500"><?= htmlspecialchars(($lead[5] ?? '') . ' ' . ($lead[6] ?? '')) ?></td>
                  <td class="p-3.5 font-black text-emerald-600"><?= htmlspecialchars($lead[7] ?? '') ?></td>
                  <td class="p-3.5 text-slate-500"><?= htmlspecialchars($lead[11] ?? 'N/A') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
