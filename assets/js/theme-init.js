(() => {
  let savedTheme;
  try { savedTheme = localStorage.getItem('rasoltek-theme'); } catch {}
  const preferredTheme = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  const theme = ['light', 'dark'].includes(savedTheme) ? savedTheme : preferredTheme;
  const root = document.documentElement;
  root.dataset.theme = theme;
  root.style.colorScheme = theme;
  root.style.backgroundColor = theme === 'dark' ? '#07111f' : '#ffffff';
  document.querySelector('meta[name="theme-color"]')?.setAttribute('content', theme === 'dark' ? '#07111f' : '#ffffff');
  document.querySelectorAll('[data-theme-icon]').forEach(icon => {
    icon.href = theme === 'dark' ? icon.dataset.darkSrc : icon.dataset.lightSrc;
  });
})();
