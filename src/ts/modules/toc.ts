/**
 * Table of Contents Generation + Active State
 */
export function initTOC(): void {
  const content = document.querySelector<HTMLElement>('.post-content');
  if (!content) return;

  const headings = content.querySelectorAll<HTMLHeadingElement>('h2, h3');
  if (headings.length === 0) {
    const tocContainer = document.getElementById('toc-container');
    const tocMobileBtn = document.getElementById('toc-mobile-btn');
    if (tocContainer) tocContainer.style.display = 'none';
    if (tocMobileBtn) tocMobileBtn.style.display = 'none';
    return;
  }

  const tocListPC = document.getElementById('toc-list');
  const tocListMobile = document.getElementById('toc-list-mobile');

  headings.forEach((heading, index) => {
    const id = 'heading-' + index;
    heading.setAttribute('id', id);

    const li = document.createElement('li');
    const a = document.createElement('a');
    a.setAttribute('href', '#' + id);
    a.textContent = heading.textContent;

    if (heading.tagName === 'H3') {
      a.classList.add('toc-h3');
    }

    li.appendChild(a);

    if (tocListPC) tocListPC.appendChild(li.cloneNode(true));
    if (tocListMobile) tocListMobile.appendChild(li.cloneNode(true));
  });

  // TOC Toggle (PC)
  const tocHeader = document.querySelector<HTMLButtonElement>('.toc-header');
  const tocContainerEl = document.getElementById('toc-container');
  if (tocHeader && tocContainerEl) {
    tocHeader.addEventListener('click', () => {
      tocContainerEl.classList.toggle('collapsed');
      const expanded = !tocContainerEl.classList.contains('collapsed');
      tocHeader.setAttribute('aria-expanded', String(expanded));
    });
  }

  // Active TOC item on scroll
  initTOCActiveState(headings);

  // Mobile TOC
  const mobileBtn = document.getElementById('toc-mobile-btn');
  const mobileOverlay = document.getElementById('toc-mobile-overlay');
  const mobileClose = document.getElementById('toc-mobile-close');

  if (mobileBtn && mobileOverlay) {
    mobileBtn.addEventListener('click', () => {
      mobileOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });

    function closeMobileTOC(): void {
      mobileOverlay!.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (mobileClose) {
      mobileClose.addEventListener('click', closeMobileTOC);
    }

    mobileOverlay.addEventListener('click', (e) => {
      if (e.target === mobileOverlay) {
        closeMobileTOC();
      }
    });

    mobileOverlay.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', closeMobileTOC);
    });
  }
}

/**
 * Highlight the active TOC item based on scroll position
 */
function initTOCActiveState(headings: NodeListOf<HTMLHeadingElement>): void {
  if (!headings.length) return;

  const tocLinks = document.querySelectorAll<HTMLAnchorElement>('#toc-list a, #toc-list-mobile a');
  if (!tocLinks.length) return;

  const headerHeight = 80;

  function updateActive(): void {
    const scrollPos = window.scrollY + headerHeight + 20;
    let currentId = '';

    headings.forEach((heading) => {
      if (heading.offsetTop <= scrollPos) {
        currentId = heading.getAttribute('id') || '';
      }
    });

    tocLinks.forEach((link) => {
      if (link.getAttribute('href') === '#' + currentId) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  window.addEventListener('scroll', updateActive, { passive: true });
  updateActive();
}
