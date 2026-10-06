(() => {
  const root = document.documentElement;
  const themeButton = document.querySelector('.theme-toggle');
  const preferredTheme = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  const setTheme = theme => {
    root.dataset.theme = theme;
    root.style.colorScheme = theme;
    root.style.backgroundColor = theme === 'dark' ? '#07111f' : '#ffffff';
    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', theme === 'dark' ? '#07111f' : '#ffffff');
    document.querySelectorAll('[data-theme-icon]').forEach(icon => {
      icon.href = theme === 'dark' ? icon.dataset.darkSrc : icon.dataset.lightSrc;
    });
    themeButton?.setAttribute('aria-label', theme === 'dark' ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    themeButton?.setAttribute('aria-pressed', String(theme === 'dark'));
  };
  setTheme(root.dataset.theme || preferredTheme);
  themeButton?.addEventListener('click', () => {
    const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
    setTheme(next);
    try { localStorage.setItem('rasoltek-theme', next); } catch {}
  });

  const header = document.querySelector('[data-header]');
  const setHeader = () => header?.classList.toggle('scrolled', scrollY > 8);
  setHeader(); addEventListener('scroll', setHeader, {passive:true});

  const menuButton = document.querySelector('.menu-btn');
  const mobileMenu = document.querySelector('#mobile-menu');
  menuButton?.addEventListener('click', () => {
    const open = menuButton.getAttribute('aria-expanded') === 'true';
    menuButton.setAttribute('aria-expanded', String(!open));
    menuButton.setAttribute('aria-label', open ? 'Buka menu' : 'Tutup menu');
    mobileMenu.hidden = open;
  });

  document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
    const trigger = dropdown.querySelector('button');
    let pinned = false;
    const setOpen = open => {
      dropdown.classList.toggle('is-open', open);
      trigger.setAttribute('aria-expanded', String(open));
    };
    const close = () => { pinned = false; setOpen(false); };
    trigger.addEventListener('click', () => { pinned = !pinned; setOpen(pinned); });
    dropdown.addEventListener('pointerenter', () => setOpen(true));
    dropdown.addEventListener('pointerleave', () => { if (!pinned && !dropdown.contains(document.activeElement)) setOpen(false); });
    dropdown.addEventListener('focusin', () => setOpen(true));
    dropdown.addEventListener('focusout', event => { if (!dropdown.contains(event.relatedTarget)) close(); });
    dropdown.addEventListener('keydown', event => {
      if (event.key === 'Escape') { trigger.focus(); close(); }
    });
    document.addEventListener('click', event => { if (!dropdown.contains(event.target)) close(); });
  });

  document.querySelectorAll('[data-domain-form][data-extension-url]').forEach(async (form, pickerIndex) => {
    const select = form.querySelector('[data-domain-extension]');
    if (!select) return;
    const picker = form.querySelector('[data-domain-extension-picker]');
    const trigger = picker?.querySelector('[data-extension-trigger]');
    const selectedLabel = picker?.querySelector('[data-selected-extension]');
    const popover = picker?.querySelector('[data-extension-popover]');
    const search = picker?.querySelector('[data-extension-search]');
    const list = picker?.querySelector('[data-extension-list]');
    const optionValues = () => Array.from(select.options, option => option.value);
    const closePicker = (restoreFocus = false) => {
      if (!popover || !trigger) return;
      popover.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
      if (restoreFocus) trigger.focus();
    };
    const chooseExtension = value => {
      select.value = value;
      select.dispatchEvent(new Event('change', {bubbles:true}));
      closePicker(true);
    };
    const renderOptions = (query = '') => {
      if (!list) return;
      const needle = query.trim().toLocaleLowerCase('id-ID').replace(/^\./, '');
      const values = optionValues().filter(value => value.toLocaleLowerCase('id-ID').replace(/^\./, '').includes(needle));
      list.replaceChildren();
      values.forEach(value => {
        const option = document.createElement('button');
        option.type = 'button';
        option.className = 'domain-extension-option';
        option.dataset.value = value;
        option.setAttribute('role', 'option');
        option.setAttribute('aria-selected', String(value === select.value));
        option.textContent = value;
        option.addEventListener('click', () => chooseExtension(value));
        list.append(option);
      });
      if (values.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'domain-extension-empty';
        empty.setAttribute('role', 'status');
        empty.textContent = 'Ekstensi tidak ditemukan.';
        list.append(empty);
      }
    };
    const syncPicker = () => {
      if (selectedLabel) selectedLabel.textContent = select.value;
      renderOptions(search?.value || '');
    };
    if (picker && trigger && selectedLabel && popover && search && list) {
      const listId = `domain-extension-list-${pickerIndex + 1}`;
      list.id = listId;
      trigger.setAttribute('aria-controls', listId);
      picker.classList.add('is-enhanced');
      syncPicker();
      trigger.addEventListener('click', () => {
        const willOpen = popover.hidden;
        if (!willOpen) { closePicker(); return; }
        popover.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        search.value = '';
        renderOptions();
        requestAnimationFrame(() => search.focus());
      });
      search.addEventListener('input', () => renderOptions(search.value));
      search.addEventListener('keydown', event => {
        if (event.key === 'Escape') closePicker(true);
        if (event.key === 'Enter') {
          event.preventDefault();
          const firstMatch = list.querySelector('button');
          if (firstMatch) chooseExtension(firstMatch.dataset.value);
        }
        if (event.key === 'ArrowDown') {
          event.preventDefault();
          list.querySelector('button')?.focus();
        }
      });
      list.addEventListener('keydown', event => {
        if (event.key === 'Escape') { closePicker(true); return; }
        if (!['ArrowDown', 'ArrowUp'].includes(event.key)) return;
        event.preventDefault();
        const options = Array.from(list.querySelectorAll('button'));
        const current = options.indexOf(document.activeElement);
        const next = event.key === 'ArrowDown' ? Math.min(current + 1, options.length - 1) : Math.max(current - 1, 0);
        options[next]?.focus();
      });
      select.addEventListener('change', syncPicker);
      document.addEventListener('pointerdown', event => {
        if (!picker.contains(event.target)) closePicker();
      });
    }
    try {
      const response = await fetch(new URL(form.dataset.extensionUrl, location.href).toString(), {headers:{Accept:'application/json'}, cache:'no-store'});
      const data = await response.json();
      if (!response.ok || !data.success || !Array.isArray(data.extensions) || data.extensions.length === 0) return;
      const current = select.value || '.com';
      select.replaceChildren(...data.extensions.map(extension => {
        const option = document.createElement('option');
        option.value = extension;
        option.textContent = extension;
        option.selected = extension === current;
        return option;
      }));
      select.value = data.extensions.includes(current) ? current : data.extensions[0];
      syncPicker();
    } catch {
      /* Keep fallback extensions already rendered in HTML. */
    }
  });

  document.querySelectorAll('[data-domain-form]').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    const input = form.querySelector('[name="domain"]');
    const button = form.querySelector('button[type="submit"]');
    const result = form.querySelector('[data-domain-result]');
    const name = input.value.trim().replace(/^https?:\/\//,'').replace(/\s+/g,'');
    const ext = form.querySelector('[name="extension"]')?.value || '';
    if (!name) { input.focus(); return; }
    const target = new URL(form.action, location.href);
    target.searchParams.set('domain', name);
    target.searchParams.set('extension', ext);
    result.className = 'domain-result is-loading';
    result.textContent = 'Mengecek ketersediaan domain...';
    button.disabled = true;
    try {
      const response = await fetch(target.toString(), {headers:{Accept:'application/json'}});
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.message || 'Pencarian domain gagal.');
      const available = data.available === true;
      const unavailable = data.available === false;
      result.className = `domain-result ${available ? 'is-available' : unavailable ? 'is-unavailable' : 'is-neutral'}`;
      result.replaceChildren();
      const copy = document.createElement('div');
      const title = document.createElement('b');
      const message = document.createElement('span');
      title.textContent = data.domain;
      message.textContent = data.message || (available ? 'Domain tersedia.' : unavailable ? 'Domain tidak tersedia.' : 'Status domain berhasil dicek.');
      copy.append(title, message);
      if (data.premium && data.premium_registration_price_label) {
        const premium = document.createElement('em');
        premium.textContent = `Premium: ${data.premium_registration_price_label}`;
        copy.append(premium);
      }
      result.append(copy);
      if (available && data.order_url) {
        const order = document.createElement('a');
        order.href = data.order_url;
        order.textContent = 'Order domain';
        result.append(order);
      }
    } catch (error) {
      result.className = 'domain-result is-error';
      result.textContent = error.message || 'Pencarian domain gagal.';
    } finally {
      button.disabled = false;
    }
  }));

  const cycleMeta = {monthly:{months:1,label:'/bulan',copy:'Ditagihkan setiap bulan'},quarterly:{months:3,label:'/3 bulan',copy:'Setara {price}/bulan'},semiannual:{months:6,label:'/6 bulan',copy:'Setara {price}/bulan'},annual:{months:12,label:'/12 bulan',copy:'Setara {price}/bulan'}};
  const money = value => 'Rp' + Math.round(value).toLocaleString('id-ID');
  const updatePrices = cycle => document.querySelectorAll('[data-vps-card]:not(.hidden-location), [data-compare-row]').forEach(card => {
    const priceEl = card.querySelector('[data-price]');
    const prices = JSON.parse(priceEl.dataset.prices);
    const meta = cycleMeta[cycle];
    priceEl.textContent = money(prices[cycle]);
    card.querySelector('[data-period]').textContent = meta.label;
    card.querySelector('[data-equivalent]').textContent = meta.copy.replace('{price}', money(prices[cycle] / meta.months));
    const link = card.querySelector('.order-link');
    if (link?.dataset.baseUrl && link.dataset.baseUrl !== '#') { const url = new URL(link.dataset.baseUrl, location.origin); url.searchParams.set('billingcycle', cycle); link.href = url.toString(); }
  });
  document.querySelectorAll('[data-cycle]').forEach(btn => btn.addEventListener('click', () => {
    document.querySelectorAll('[data-cycle]').forEach(b => { b.classList.toggle('active', b===btn); b.setAttribute('aria-selected', String(b===btn)); });
    updatePrices(btn.dataset.cycle);
  }));
  document.querySelectorAll('[data-location]').forEach(btn => {
    if (!btn.matches('button')) return;
    btn.addEventListener('click', () => {
      const location = btn.dataset.location;
      document.querySelectorAll('button[data-location]').forEach(b => { b.classList.toggle('active', b===btn); b.setAttribute('aria-selected', String(b===btn)); });
      document.querySelectorAll('[data-vps-card]').forEach(card => card.classList.toggle('hidden-location', card.dataset.location !== location));
      updatePrices(document.querySelector('[data-cycle].active')?.dataset.cycle || 'monthly');
    });
  });

  document.querySelectorAll('[data-hosting-cycle]').forEach(button => {
    button.addEventListener('click', () => {
      const cycle = button.dataset.hostingCycle;
      const meta = cycleMeta[cycle];
      document.querySelectorAll('[data-hosting-cycle]').forEach(tab => {
        tab.classList.toggle('active', tab === button);
        tab.setAttribute('aria-pressed', String(tab === button));
      });
      document.querySelectorAll('[data-hosting-card]').forEach(card => {
        const prices = JSON.parse(card.dataset.hostingPrices);
        const price = prices[cycle];
        const baseline = prices.monthly * meta.months;
        const discounted = price < baseline;
        card.querySelector('[data-hosting-price]').textContent = money(price);
        card.querySelector('[data-hosting-period]').textContent = meta.label;
        card.querySelector('[data-hosting-equivalent]').textContent = cycle === 'monthly'
          ? 'Ditagihkan setiap bulan.'
          : `Setara ${money(price / meta.months)}/bulan. Dibayar per ${meta.months} bulan.`;
        card.querySelector('[data-hosting-promo]').hidden = !discounted;
        card.querySelector('[data-hosting-promo-note]').hidden = !discounted;
        card.querySelector('[data-hosting-original]').textContent = money(baseline);
        const percentage = ((baseline - price) / baseline * 100).toLocaleString('id-ID', {maximumFractionDigits:1});
        card.querySelector('[data-hosting-saving]').textContent = discounted ? `Hemat ${percentage}%` : '';
        const link = card.querySelector('[data-hosting-order]');
        const url = new URL(link.dataset.baseUrl, location.origin);
        url.searchParams.set('billingcycle', cycle);
        link.href = url.toString();
      });
    });
  });

  document.querySelectorAll('[data-contact-form]').forEach(form => {
    form.addEventListener('submit', event => {
      event.preventDefault();
      if (!form.reportValidity()) return;
      const fields = new FormData(form);
      const name = String(fields.get('name') || '').trim();
      const email = String(fields.get('email') || '').trim();
      const subject = String(fields.get('subject') || '').trim();
      const message = String(fields.get('body') || '').trim();
      const body = `Nama: ${name}\r\nEmail: ${email}\r\n\r\nPesan:\r\n${message.replace(/\r?\n/g, '\r\n')}`;
      const mailto = `mailto:admin@rasoltek.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
      form.querySelector('[data-contact-mail-link]').href = mailto;
      form.querySelector('[data-contact-status]').hidden = false;
      window.location.href = mailto;
    });
  });

  const revealTargets = document.querySelectorAll('.section-head, .product-card, .price-card, .feature, .step, .domain-box, .infra-copy, .infra-map, .faq-item, .contact-card, .metric');
  const allowRevealMotion = matchMedia('(min-width: 961px) and (prefers-reduced-motion: no-preference)').matches;
  if ('IntersectionObserver' in window && allowRevealMotion) {
    const belowFold = [...revealTargets].filter(el => el.getBoundingClientRect().top >= innerHeight);
    belowFold.forEach((el, index) => { el.classList.add('reveal'); el.style.setProperty('--delay', `${(index % 3) * 50}ms`); });
    const observer = new IntersectionObserver(entries => entries.forEach(entry => {
      if (entry.isIntersecting) { entry.target.classList.add('revealed'); observer.unobserve(entry.target); }
    }), {threshold:.08});
    belowFold.forEach(el => observer.observe(el));
  }

  const panel = document.querySelector('.cloud-panel');
  if (panel && matchMedia('(pointer:fine)').matches && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    panel.addEventListener('pointermove', event => {
      const box = panel.getBoundingClientRect();
      const rx = ((event.clientY - box.top) / box.height - .5) * -3;
      const ry = ((event.clientX - box.left) / box.width - .5) * 3;
      panel.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-4px)`;
      panel.style.setProperty('--mx', `${event.clientX - box.left}px`); panel.style.setProperty('--my', `${event.clientY - box.top}px`);
    });
    panel.addEventListener('pointerleave', () => panel.style.transform = '');
  }
})();
