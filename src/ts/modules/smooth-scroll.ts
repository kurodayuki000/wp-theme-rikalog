/**
 * Smooth Scroll for anchor links
 */
export function initSmoothScroll(): void {
  document.addEventListener('click', (e) => {
    const link = (e.target as HTMLElement).closest<HTMLAnchorElement>('a[href^="#"]');
    if (!link) return;

    const targetId = link.getAttribute('href')!.slice(1);
    if (!targetId) return;

    const target = document.getElementById(targetId);
    if (!target) return;

    e.preventDefault();

    const header = document.querySelector<HTMLElement>('.site-header');
    const headerHeight = header ? header.offsetHeight : 0;
    const top = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

    window.scrollTo({
      top,
      behavior: 'smooth',
    });
  });
}
