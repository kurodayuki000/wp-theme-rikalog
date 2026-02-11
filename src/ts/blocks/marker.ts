/**
 * Marker (Highlighter) — RichText Inline Format
 */

/// <reference path="../types/wordpress.d.ts" />
export {}; // ensure this file is treated as a module

const { registerFormatType, applyFormat, removeFormat } = wp.richText;
const { createElement: el, Fragment, useState, useRef, useEffect } = wp.element;
const { RichTextToolbarButton } = wp.blockEditor;
const { __ } = wp.i18n;

interface MarkerColor {
  name: string;
  slug: string;
  color: string;
}

const COLORS: MarkerColor[] = [
  { name: __('黄色', 'rikalog'),     slug: 'yellow', color: '#FFE066' },
  { name: __('ピンク', 'rikalog'),   slug: 'pink',   color: '#F8A5C2' },
  { name: __('青', 'rikalog'),       slug: 'blue',   color: '#82C4FF' },
  { name: __('緑', 'rikalog'),       slug: 'green',  color: '#81D496' },
  { name: __('オレンジ', 'rikalog'), slug: 'orange', color: '#FFB74D' },
];

const FORMAT_NAME = 'rikalog/marker';

const markerIcon = el('svg', {
  width: 20, height: 20, viewBox: '0 0 24 24', fill: 'none',
  xmlns: 'http://www.w3.org/2000/svg'
},
  el('path', {
    d: 'M15.243 4.515l-6.738 6.737-.707 2.121-1.04 1.041 2.828 2.829 1.04-1.041 2.122-.707 6.737-6.738-4.242-4.242zm6.364 3.535a1 1 0 010 1.414l-7.778 7.778-2.122.707-1.414 1.414a1 1 0 01-1.414 0l-4.243-4.243a1 1 0 010-1.414l1.414-1.414.707-2.121 7.779-7.779a1 1 0 011.414 0l5.657 5.658z',
    fill: '#D4A0A0',
  }),
  el('path', { d: 'M3 20h18v2H3z', fill: '#D4A0A0' })
);

interface MarkerButtonProps {
  value: wp.richText.RichTextValue;
  onChange: (value: wp.richText.RichTextValue) => void;
  isActive: boolean;
}

function MarkerButton(props: MarkerButtonProps) {
  const { value, onChange, isActive } = props;
  const [showPicker, setShowPicker] = useState(false);
  const buttonRef = useRef<HTMLDivElement | null>(null);
  const pickerRef = useRef<HTMLDivElement | null>(null);

  // Close picker when clicking outside
  useEffect(() => {
    if (!showPicker) return;
    function handleClick(e: MouseEvent): void {
      if (pickerRef.current && !pickerRef.current.contains(e.target as Node) &&
          buttonRef.current && !buttonRef.current.contains(e.target as Node)) {
        setShowPicker(false);
      }
    }
    document.addEventListener('mousedown', handleClick);
    return () => {
      document.removeEventListener('mousedown', handleClick);
    };
  }, [showPicker]);

  function onSelectColor(slug: string): void {
    const className = 'rikalog-marker-' + slug;

    // If already active with this color, remove it
    if (isActive && value.activeFormats) {
      const current = value.activeFormats.find((f) => f.type === FORMAT_NAME);
      if (current && current.attributes && current.attributes.class === className) {
        onChange(removeFormat(value, FORMAT_NAME));
        setShowPicker(false);
        return;
      }
    }

    onChange(applyFormat(value, {
      type: FORMAT_NAME,
      attributes: { class: className },
    }));
    setShowPicker(false);
  }

  function onRemoveMarker(): void {
    onChange(removeFormat(value, FORMAT_NAME));
    setShowPicker(false);
  }

  return el(Fragment, {},
    el('div', { ref: buttonRef, style: { display: 'inline-block' } },
      el(RichTextToolbarButton, {
        icon: markerIcon,
        title: __('マーカー', 'rikalog'),
        isActive,
        onClick: () => { setShowPicker(!showPicker); },
      })
    ),
    showPicker && el('div', { ref: pickerRef, className: 'rikalog-marker-picker' },
      el('div', { className: 'rikalog-marker-picker-colors' },
        COLORS.map((c) =>
          el('button', {
            key: c.slug,
            className: 'rikalog-marker-picker-btn',
            title: c.name,
            onClick: () => { onSelectColor(c.slug); },
            style: { background: c.color },
          })
        )
      ),
      isActive && el('button', {
        className: 'rikalog-marker-picker-remove',
        onClick: onRemoveMarker,
      }, __('マーカーを解除', 'rikalog'))
    )
  );
}

registerFormatType(FORMAT_NAME, {
  title: __('マーカー', 'rikalog'),
  tagName: 'span',
  className: 'rikalog-marker',
  attributes: { class: 'class' },
  edit: MarkerButton,
});

// Inject picker styles into the editor
const styleEl = document.createElement('style');
styleEl.textContent = `
.rikalog-marker-picker {
  position: absolute;
  z-index: 100000;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px 12px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 4px;
}
.rikalog-marker-picker-colors {
  display: flex;
  gap: 6px;
}
.rikalog-marker-picker-btn {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  transition: transform 0.15s ease, border-color 0.15s ease;
  padding: 0;
}
.rikalog-marker-picker-btn:hover {
  transform: scale(1.2);
  border-color: #3C3C3C;
}
.rikalog-marker-picker-remove {
  background: none;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 4px 8px;
  font-size: 12px;
  cursor: pointer;
  color: #666;
  transition: background 0.15s ease;
}
.rikalog-marker-picker-remove:hover {
  background: #f5f5f5;
}`;
document.head.appendChild(styleEl);
