import type { Config } from 'tailwindcss';

export default {
  content: [
    './**/*.php',
    './src/ts/**/*.ts',
  ],
  darkMode: ['selector', '[data-theme="dark"]'],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: 'var(--color-primary)',
          hover: 'var(--color-primary-hover)',
          deep: 'var(--color-primary-deep)',
          light: 'var(--color-primary-light)',
        },
        accent: {
          DEFAULT: 'var(--color-accent)',
          light: 'var(--color-accent-light)',
        },
        surface: {
          DEFAULT: 'var(--color-surface)',
          hover: 'var(--color-surface-hover)',
          elevated: 'var(--color-surface-elevated)',
        },
        bg: {
          DEFAULT: 'var(--color-bg)',
          white: 'var(--color-bg-white)',
        },
        text: {
          DEFAULT: 'var(--color-text)',
          secondary: 'var(--color-text-secondary)',
          muted: 'var(--color-text-muted)',
        },
        border: {
          DEFAULT: 'var(--color-border)',
          light: 'var(--color-border-light)',
        },
        overlay: 'var(--color-overlay)',
        'code-bg': 'var(--color-code-bg)',
        'blockquote-bg': 'var(--color-blockquote-bg)',
        'blockquote-border': 'var(--color-blockquote-border)',
        'tag-bg': 'var(--color-tag-bg)',
        'tag-text': 'var(--color-tag-text)',
        'input-bg': 'var(--color-input-bg)',
        'input-border': 'var(--color-input-border)',
        'header-bg': 'var(--header-bg)',
        'footer-bg': 'var(--footer-bg)',
        'footer-surface': 'var(--footer-surface)',
        'footer-text': 'var(--footer-text)',
        'card-bg': 'var(--color-card-bg)',
        'card-border': 'var(--color-card-border)',
      },
      backgroundImage: {
        'gradient-primary': 'var(--gradient-primary)',
        'gradient-hero': 'var(--gradient-hero)',
        'gradient-card': 'var(--gradient-card)',
      },
      boxShadow: {
        sm: '0 2px 12px var(--color-shadow)',
        md: '0 4px 20px var(--color-shadow-md)',
        lg: '0 8px 32px var(--color-shadow-lg)',
        hover: '0 8px 30px var(--color-shadow-hover)',
        glow: 'var(--glow-primary)',
      },
      fontFamily: {
        sans: ['"Noto Sans JP"', '"Hiragino Kaku Gothic ProN"', '"Hiragino Sans"', '"Yu Gothic"', '"Meiryo"', 'sans-serif'],
        mono: ['"SFMono-Regular"', '"Consolas"', '"Liberation Mono"', '"Menlo"', 'monospace'],
      },
      transitionTimingFunction: {
        bounce: 'cubic-bezier(0.22, 1, 0.36, 1)',
      },
      keyframes: {
        slideUp: {
          from: { transform: 'translateY(100%)', opacity: '0' },
          to: { transform: 'translateY(0)', opacity: '1' },
        },
        shake: {
          '0%, 100%': { transform: 'translateX(0)' },
          '20%': { transform: 'translateX(-4px)' },
          '40%': { transform: 'translateX(4px)' },
          '60%': { transform: 'translateX(-3px)' },
          '80%': { transform: 'translateX(3px)' },
        },
        fadeInMsg: {
          from: { opacity: '0', transform: 'translateY(-4px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        fadeInUp: {
          from: { opacity: '0', transform: 'translateY(24px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
      },
      animation: {
        'slide-up': 'slideUp 0.4s cubic-bezier(0.22, 1, 0.36, 1)',
        shake: 'shake 0.4s ease',
        'fade-in-msg': 'fadeInMsg 0.25s ease',
        'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1)',
      },
      maxWidth: {
        container: '1140px',
        content: '780px',
        narrow: '640px',
      },
    },
  },
  plugins: [],
} satisfies Config;
