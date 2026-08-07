/* ==========================================================
   main.js — Global helpers: nav, FAQ, mobile menu, formatting
   ========================================================== */

document.addEventListener('DOMContentLoaded', () => {
  /* Robust Mobile Menu Toggle */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#mobileMenuBtn');
    const menu = document.getElementById('mobileMenu');

    if (btn && menu) {
      e.stopPropagation();
      menu.classList.toggle('hidden');
      const icon = btn.querySelector('[data-lucide], svg');
      if (icon && window.lucide) {
        icon.setAttribute('data-lucide', menu.classList.contains('hidden') ? 'menu' : 'x');
        lucide.createIcons();
      }
      return;
    }

    /* Close mobile menu on outside click */
    if (menu && !menu.classList.contains('hidden')) {
      if (!menu.contains(e.target)) {
        menu.classList.add('hidden');
      }
    }
  });

  /* FAQ Accordion — event delegation (works with dynamic items) */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.faq-btn');
    if (!btn) return;
    const item = btn.closest('.faq-item');
    if (!item) return;
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  });

  /* Smooth scroll for anchor links */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const href = a.getAttribute('href');
      if (!href || href === '#') return;
      try {
        const target = document.querySelector(href);
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
      } catch (err) {}
    });
  });

  /* Re-create Lucide icons */
  window.refreshIcons = () => {
    if (window.lucide) lucide.createIcons();
  };
});

/* Utility: format date to readable */
window.fmtDate = function(iso) {
  if (!iso) return '';
  const d = new Date(iso.split('T')[0] + 'T00:00:00');
  return isNaN(d) ? iso : d.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
};

/* Utility: format time HH:MM from ISO */
window.fmtTime = function(iso) {
  if (!iso) return '--';
  const parts = iso.split('T');
  if (parts.length < 2) return '--';
  return parts[1].substring(0, 5);
};

/* Utility: format duration in minutes to Xh Ym */
window.fmtDur = function(mins) {
  if (!mins || isNaN(mins)) return 'N/A';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  if (h === 0) return `${m}m`;
  if (m === 0) return `${h}h`;
  return `${h}h ${m}m`;
};
