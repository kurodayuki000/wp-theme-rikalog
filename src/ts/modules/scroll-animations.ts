/**
 * Scroll-based Fade-in Animations
 */
export function initScrollAnimations(): void {
  const targets = document.querySelectorAll<HTMLElement>(
    '.post-card, .sidebar-widget, .about-content, .contact-form, ' +
    '.archive-header, .single-post-header, .single-post-thumbnail, ' +
    '.comments-area, .error-404-page, ' +
    '.hero-section, .front-section, .front-post-card, .front-category-card, ' +
    '.front-about-teaser, .contact-header, .contact-form-wrap, ' +
    '.about-header, .about-avatar, .about-page h2, ' +
    '.search-results-header, .no-results, ' +
    '.toc-container, .post-content, .single-post-tags, .post-navigation'
  );
  if (!targets.length) return;

  // Add the animation class
  targets.forEach((el) => {
    el.classList.add('fade-in-up');
  });

  // Mark parent lists for stagger
  const staggerContainers = document.querySelectorAll<HTMLElement>(
    '.post-list, .front-post-grid, .front-category-grid'
  );
  staggerContainers.forEach((el) => {
    el.classList.add('stagger-children');
  });

  if ('IntersectionObserver' in window) {
    const initialBatch: HTMLElement[] = [];
    const scrollTargets: HTMLElement[] = [];
    const viewportH = window.innerHeight;

    targets.forEach((el) => {
      const rect = el.getBoundingClientRect();
      if (rect.top < viewportH && rect.bottom > 0) {
        initialBatch.push(el);
      } else {
        scrollTargets.push(el);
      }
    });

    // Initial batch: stagger fade-in
    setTimeout(() => {
      initialBatch.forEach((el, i) => {
        const inStagger = el.closest('.stagger-children');
        if (!inStagger) {
          el.style.transitionDelay = (i * 0.12) + 's';
        }
        el.classList.add('is-visible');
      });
    }, 100);

    // Scroll observer for remaining elements
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            (entry.target as HTMLElement).classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.05, rootMargin: '0px 0px -30px 0px' }
    );

    scrollTargets.forEach((el) => {
      observer.observe(el);
    });
  } else {
    targets.forEach((el) => {
      el.classList.add('is-visible');
    });
  }
}
