/**
 * Dark Mode Toggle
 */
export function initDarkMode(): void {
  const toggle = document.querySelector<HTMLButtonElement>('.theme-toggle');
  if (!toggle) return;

  toggle.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('rikalog-theme', next);
  });
}
