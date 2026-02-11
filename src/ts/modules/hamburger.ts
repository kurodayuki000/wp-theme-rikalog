/**
 * Hamburger Menu (slide-in)
 */
export function initHamburger(): void {
  const hamburger = document.querySelector<HTMLButtonElement>('.hamburger');
  const overlay = document.querySelector<HTMLElement>('.mobile-nav-overlay');
  if (!hamburger || !overlay) return;

  hamburger.addEventListener('click', () => {
    const isActive = hamburger.classList.toggle('active');
    overlay.classList.toggle('active');
    hamburger.setAttribute('aria-expanded', String(isActive));
    overlay.setAttribute('aria-hidden', String(!isActive));
    document.body.style.overflow = isActive ? 'hidden' : '';
  });

  // Close menu when link clicked
  overlay.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('active');
      overlay.classList.remove('active');
      hamburger.setAttribute('aria-expanded', 'false');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    });
  });
}
