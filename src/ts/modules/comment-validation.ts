/**
 * Comment Form Validation
 */

interface ValidationRule {
  required: boolean;
  email?: boolean;
  label: string;
}

export function initCommentValidation(): void {
  const form = document.getElementById('commentform') as HTMLFormElement | null;
  if (!form) return;

  const rules: Record<string, ValidationRule> = {
    author:  { required: true, label: '名前' },
    email:   { required: true, email: true, label: 'メールアドレス' },
    comment: { required: true, label: 'コメント' },
  };

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function getField(name: string): HTMLInputElement | HTMLTextAreaElement | null {
    return form!.querySelector(`[name="${name}"]`);
  }

  function showError(field: HTMLElement, msg: string): void {
    clearError(field);
    field.classList.add('field-error');
    const el = document.createElement('span');
    el.className = 'field-error-msg';
    el.textContent = msg;
    field.parentNode!.insertBefore(el, field.nextSibling);
  }

  function clearError(field: HTMLElement): void {
    field.classList.remove('field-error');
    const msg = field.parentNode!.querySelector('.field-error-msg');
    if (msg) msg.remove();
  }

  function showOk(field: HTMLElement): void {
    clearError(field);
    field.classList.add('field-ok');
  }

  function clearOk(field: HTMLElement): void {
    field.classList.remove('field-ok');
  }

  function validate(name: string): boolean {
    const rule = rules[name];
    if (!rule) return true;
    const field = getField(name);
    if (!field) return true;

    clearOk(field);
    const val = field.value.trim();

    if (rule.required && !val) {
      showError(field, rule.label + 'を入力してください');
      return false;
    }

    if (rule.email && val && !emailRe.test(val)) {
      showError(field, 'メールアドレスの形式が正しくありません');
      return false;
    }

    clearError(field);
    if (val) showOk(field);
    return true;
  }

  // Attach blur + input listeners
  Object.keys(rules).forEach((name) => {
    const field = getField(name);
    if (!field) return;

    field.addEventListener('blur', () => {
      validate(name);
    });

    field.addEventListener('input', () => {
      if (field.classList.contains('field-error')) {
        validate(name);
      }
    });
  });

  // Submit validation
  form.addEventListener('submit', (e) => {
    let valid = true;
    Object.keys(rules).forEach((name) => {
      if (!validate(name)) valid = false;
    });
    if (!valid) {
      e.preventDefault();
      const first = form.querySelector<HTMLElement>('.field-error');
      if (first) first.focus();
    }
  });
}
