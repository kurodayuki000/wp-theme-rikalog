/**
 * Search Form: prevent empty search
 */
export function initSearchForm(): void {
  const forms = document.querySelectorAll<HTMLFormElement>('.search-form');
  forms.forEach((form) => {
    const field = form.querySelector<HTMLInputElement>('.search-field');
    if (field) {
      field.setAttribute('required', 'required');
    }
    form.addEventListener('submit', (e) => {
      if (!field || field.value.trim() === '') {
        e.preventDefault();
        if (field) {
          field.focus();
          field.classList.add('search-field--empty');
          setTimeout(() => {
            field.classList.remove('search-field--empty');
          }, 600);
        }
      }
    });
  });
}
