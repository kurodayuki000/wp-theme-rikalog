/**
 * Sidebar Select Navigation
 */
export function initSidebarSelects(): void {
  function setupSelect(id: string): void {
    const select = document.getElementById(id) as HTMLSelectElement | null;
    if (select) {
      select.addEventListener('change', function () {
        if (this.value) window.location.href = this.value;
      });
    }
  }

  setupSelect('sidebar-cat-select');
  setupSelect('sidebar-tag-select');
  setupSelect('sidebar-archive-select');
}
