/**
 * RikaLog Theme JavaScript
 * Main entry point — imports all modules
 */

import { initDarkMode } from './modules/dark-mode';
import { initHeaderScroll } from './modules/header-scroll';
import { initScrollAnimations } from './modules/scroll-animations';
import { initTOC } from './modules/toc';
import { initSmoothScroll } from './modules/smooth-scroll';
import { initHamburger } from './modules/hamburger';
import { initSearchForm } from './modules/search-form';
import { initCommentValidation } from './modules/comment-validation';
import { initSidebarSelects } from './modules/sidebar-selects';

document.addEventListener('DOMContentLoaded', () => {
  initDarkMode();
  initHeaderScroll();
  initScrollAnimations();
  initTOC();
  initSmoothScroll();
  initHamburger();
  initSearchForm();
  initCommentValidation();
  initSidebarSelects();
});
