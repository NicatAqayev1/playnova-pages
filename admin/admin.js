(() => {
  const form = document.getElementById('editor');
  document.querySelectorAll('[data-confirm]').forEach(b => b.addEventListener('click', e => { if (!confirm(b.dataset.confirm)) e.preventDefault(); }));
  if (!form) return;
  const dirty = form.querySelector('.dirty');
  let changed = false;
  const mark = () => { changed = true; dirty.hidden = false; };
  const esc = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

  // Siyahı elementlərinin adlarındakı indeksi DOM sırasına görə yenidən yazır
  const renum = list => {
    const base = list.dataset.name, re = new RegExp('^' + esc(base) + '\\[\\d+\\]');
    [...list.children].forEach((it, i) => it.querySelectorAll('[name]').forEach(el => el.name = el.name.replace(re, base + '[' + i + ']')));
    const wrap = list.closest('.list-wrap'), max = +wrap.dataset.max, n = list.children.length;
    wrap.querySelector('.count').textContent = '(' + n + ')';
    wrap.querySelector('.btn-add').disabled = max > 0 && n >= max;
  };
  form.querySelectorAll('.list').forEach(renum);

  form.addEventListener('input', e => {
    mark();
    if (e.target.hasAttribute('data-title')) {
      const t = e.target.closest('.item').querySelector('.item-title');
      t.textContent = e.target.value.trim() || 'Yeni element';
    }
  });
  form.addEventListener('change', mark);

  form.addEventListener('click', e => {
    const add = e.target.closest('.btn-add');
    if (add) {
      const wrap = add.closest('.list-wrap'), list = wrap.querySelector('.list');
      list.appendChild(wrap.querySelector('template').content.cloneNode(true));
      renum(list); mark();
      const last = list.lastElementChild; last.open = true; last.querySelector('input[type=text],textarea')?.focus();
      return;
    }
    const b = e.target.closest('.ib');
    if (!b) return;
    e.preventDefault();
    const item = b.closest('.item'), list = item.parentElement;
    if (b.dataset.act === 'up' && item.previousElementSibling) list.insertBefore(item, item.previousElementSibling);
    if (b.dataset.act === 'down' && item.nextElementSibling) list.insertBefore(item.nextElementSibling, item);
    if (b.dataset.act === 'del' && confirm('Bu element silinsin?')) item.remove();
    renum(list); mark();
  });

  form.addEventListener('submit', () => { changed = false; form.querySelectorAll('.list').forEach(renum); });
  addEventListener('beforeunload', e => { if (changed) { e.preventDefault(); e.returnValue = ''; } });
})();
