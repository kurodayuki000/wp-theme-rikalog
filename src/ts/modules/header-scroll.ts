/**
 * Header Scroll Effect
 */
export function initHeaderScroll(): void {
  const header = document.querySelector<HTMLElement>('.site-header');
  if (!header) return;

  let scrolled = false;

  function onScroll(): void {
    const shouldAdd = window.scrollY > 10;
    if (shouldAdd !== scrolled) {
      scrolled = shouldAdd;
      header!.classList.toggle('scrolled', scrolled);
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}
