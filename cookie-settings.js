(() => {
  const KEY = 'playnova_cookie_consent';
  const root = document.querySelector('[data-consent]');
  if (!root) return;
  const switches = root.querySelectorAll('.switch[data-category]');
  const status = root.querySelector('.consent-status');
  const months = ['yanvar','fevral','mart','aprel','may','iyun','iyul','avqust','sentyabr','oktyabr','noyabr','dekabr'];
  const fmt = iso => { const d = new Date(iso); return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear(); };

  const read = () => { try { return JSON.parse(localStorage.getItem(KEY)); } catch (e) { return null; } };
  const write = value => { try { localStorage.setItem(KEY, JSON.stringify(value)); return true; } catch (e) { return false; } };
  const setSwitch = (el, on) => { el.setAttribute('aria-checked', String(on)); };
  const state = () => ({
    necessary: true,
    analytics: root.querySelector('[data-category="analytics"]').getAttribute('aria-checked') === 'true',
    marketing: root.querySelector('[data-category="marketing"]').getAttribute('aria-checked') === 'true'
  });
  const save = (choice, message) => {
    const ok = write({ ...choice, updated: new Date().toISOString() });
    status.textContent = ok ? message + ' (' + fmt(new Date().toISOString()) + ')' : 'Seçim yadda saxlanıla bilmədi. Brauzerinizin yaddaş parametrlərini yoxlayın.';
  };

  const saved = read();
  if (saved) {
    setSwitch(root.querySelector('[data-category="analytics"]'), !!saved.analytics);
    setSwitch(root.querySelector('[data-category="marketing"]'), !!saved.marketing);
    if (saved.updated) status.textContent = 'Son seçiminiz: ' + fmt(saved.updated) + '.';
  }

  switches.forEach(el => {
    if (el.getAttribute('aria-disabled') === 'true') return;
    el.addEventListener('click', () => setSwitch(el, el.getAttribute('aria-checked') !== 'true'));
  });
  root.querySelector('[data-action="accept-all"]').addEventListener('click', () => {
    switches.forEach(el => setSwitch(el, true));
    save({ necessary: true, analytics: true, marketing: true }, 'Bütün kukilər qəbul edildi');
  });
  root.querySelector('[data-action="necessary-only"]').addEventListener('click', () => {
    switches.forEach(el => { if (el.dataset.category !== 'necessary') setSwitch(el, false); });
    save({ necessary: true, analytics: false, marketing: false }, 'Yalnız zəruri kukilər saxlanıldı');
  });
  root.querySelector('[data-action="save"]').addEventListener('click', () => {
    save(state(), 'Seçiminiz yadda saxlanıldı');
  });
})();
