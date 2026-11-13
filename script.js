const navGroups = document.querySelectorAll('.nav-group');
navGroups.forEach(group => {
  const trigger = group.querySelector('.dropdown-trigger');
  trigger.addEventListener('click', () => {
    const wasOpen = group.classList.contains('open');
    navGroups.forEach(item => item.classList.remove('open'));
    if (!wasOpen) group.classList.add('open');
  });
});
document.addEventListener('click', event => {
  if (!event.target.closest('.nav-group')) navGroups.forEach(group => group.classList.remove('open'));
});
const toggle = document.querySelector('.mobile-toggle');
const nav = document.querySelector('.main-nav');
toggle.addEventListener('click', () => {
  const open = nav.classList.toggle('open');
  toggle.setAttribute('aria-expanded', String(open));
  toggle.textContent = open ? '×' : '☰';
});
const modal = document.querySelector('.search-modal');
const searchInput = document.querySelector('#site-search');
function openSearch(){ modal.classList.add('show'); setTimeout(() => searchInput.focus(), 50); }
document.querySelector('.search-btn').addEventListener('click', openSearch);
document.querySelector('.close-search').addEventListener('click', () => modal.classList.remove('show'));
modal.addEventListener('click', event => { if (event.target === modal) modal.classList.remove('show'); });
document.addEventListener('keydown', event => { if (event.key === 'Escape') modal.classList.remove('show'); });
searchInput.addEventListener('keydown', event => {
  if (event.key === 'Enter' && searchInput.value.trim()) {
    const term = searchInput.value.trim().toLowerCase();
    const has = words => words.some(w => term.includes(w));
    const target = has(['game','slot']) ? '#games' : has(['guide']) ? '#guides' : has(['responsible','help','addiction','age','21']) ? '#responsible' : has(['legal','licen','terms','privacy','operator']) ? '#legal' : '#casinos';
    modal.classList.remove('show');
    if (has(['cookie'])) { location.href = 'cookies.php'; return; }
    if (has(['privacy','data'])) { location.href = 'privacy.php'; return; }
    if (has(['terms','conditions','copyright'])) { location.href = 'terms.php'; return; }
    const section = document.querySelector(target);
    if (section) section.scrollIntoView({behavior:'smooth'}); else location.href = 'index.php' + target;
  }
});
