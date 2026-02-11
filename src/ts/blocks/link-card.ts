/**
 * Link Card Block — Gutenberg editor registration
 */

/// <reference path="../types/wordpress.d.ts" />
export {}; // ensure this file is treated as a module

const { registerBlockType } = wp.blocks;
const { createElement: el, Fragment, useState } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl, ToggleControl, Button, Spinner } = wp.components;
const { __ } = wp.i18n;
const apiFetch = wp.apiFetch;

interface LinkCardAttributes {
  url: string;
  title: string;
  description: string;
  image: string;
  site_name: string;
  favicon: string;
  nofollow: boolean;
}

interface OGPResponse {
  title: string;
  description: string;
  image: string;
  site_name: string;
  favicon: string;
}

const linkIcon = el('svg', {
  width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none',
  xmlns: 'http://www.w3.org/2000/svg'
},
  el('path', {
    d: 'M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71',
    stroke: '#D4A0A0', strokeWidth: 2, strokeLinecap: 'round', strokeLinejoin: 'round'
  }),
  el('path', {
    d: 'M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71',
    stroke: '#D4A0A0', strokeWidth: 2, strokeLinecap: 'round', strokeLinejoin: 'round'
  })
);

registerBlockType('rikalog/link-card', {
  title: __('リンクカード', 'rikalog'),
  description: __('URLからOGP情報を取得してカード形式で表示します', 'rikalog'),
  icon: linkIcon,
  category: 'embed',
  keywords: ['link', 'card', 'ogp', 'url', __('リンク', 'rikalog'), __('カード', 'rikalog')],
  attributes: {
    url:         { type: 'string', default: '' },
    title:       { type: 'string', default: '' },
    description: { type: 'string', default: '' },
    image:       { type: 'string', default: '' },
    site_name:   { type: 'string', default: '' },
    favicon:     { type: 'string', default: '' },
    nofollow:    { type: 'boolean', default: false },
  },
  supports: {
    html: false,
    align: false,
  },

  edit(props) {
    const attributes = props.attributes as unknown as LinkCardAttributes;
    const setAttributes = props.setAttributes;
    const [isLoading, setIsLoading] = useState(false);

    function fetchOGP(): void {
      if (!attributes.url) return;
      setIsLoading(true);
      apiFetch({
        path: '/rikalog/v1/ogp?url=' + encodeURIComponent(attributes.url),
      }).then((data: OGPResponse) => {
        setAttributes({
          title:       data.title || '',
          description: data.description || '',
          image:       data.image || '',
          site_name:   data.site_name || '',
          favicon:     data.favicon || '',
        });
        setIsLoading(false);
      }).catch(() => {
        setIsLoading(false);
      });
    }

    const hasData = attributes.title || attributes.image;

    const sidebarPanel = el(InspectorControls, {},
      el(PanelBody, { title: __('リンクカード設定', 'rikalog'), initialOpen: true },
        el(TextControl, {
          label: 'URL',
          value: attributes.url,
          onChange: (val: string) => { setAttributes({ url: val }); },
          placeholder: 'https://...',
        }),
        el('div', { style: { marginBottom: '16px' } },
          el(Button, {
            variant: 'secondary',
            onClick: fetchOGP,
            disabled: !attributes.url || isLoading,
            style: { width: '100%', justifyContent: 'center' },
          }, isLoading
            ? el(Fragment, {}, el(Spinner, null), ' ' + __('取得中...', 'rikalog'))
            : __('OGP情報を取得', 'rikalog')
          )
        ),
        el(TextControl, {
          label: __('タイトル', 'rikalog'),
          value: attributes.title,
          onChange: (val: string) => { setAttributes({ title: val }); },
        }),
        el(TextControl, {
          label: __('説明文', 'rikalog'),
          value: attributes.description,
          onChange: (val: string) => { setAttributes({ description: val }); },
        }),
        el(TextControl, {
          label: __('画像URL', 'rikalog'),
          value: attributes.image,
          onChange: (val: string) => { setAttributes({ image: val }); },
        }),
        el(TextControl, {
          label: __('サイト名', 'rikalog'),
          value: attributes.site_name,
          onChange: (val: string) => { setAttributes({ site_name: val }); },
        }),
        el(TextControl, {
          label: __('ファビコンURL', 'rikalog'),
          value: attributes.favicon,
          onChange: (val: string) => { setAttributes({ favicon: val }); },
        }),
        el(ToggleControl, {
          label: 'nofollow',
          help: __('外部リンクにnofollow属性を付与', 'rikalog'),
          checked: attributes.nofollow,
          onChange: (val: boolean) => { setAttributes({ nofollow: val }); },
        })
      )
    );

    let preview;

    if (hasData) {
      preview = el('div', { className: 'link-card-editor' },
        el('div', { className: 'link-card' },
          attributes.image
            ? el('div', { className: 'link-card-thumbnail' },
                el('img', { src: attributes.image, alt: attributes.title || '' })
              )
            : null,
          el('div', { className: 'link-card-content' },
            el('div', { className: 'link-card-title' }, attributes.title || attributes.url),
            attributes.description
              ? el('div', { className: 'link-card-description' }, attributes.description)
              : null,
            el('div', { className: 'link-card-meta' },
              attributes.favicon
                ? el('img', { className: 'link-card-favicon', src: attributes.favicon, alt: '', width: 16, height: 16 })
                : null,
              el('span', { className: 'link-card-domain' }, attributes.site_name || attributes.url)
            )
          )
        )
      );
    } else {
      preview = el('div', { className: 'link-card-placeholder' },
        el('div', { className: 'link-card-placeholder-inner' },
          linkIcon,
          el('p', null, __('リンクカード', 'rikalog')),
          el('p', { style: { fontSize: '13px', color: '#999' } },
            __('サイドバーでURLを入力し、「OGP情報を取得」ボタンをクリックしてください', 'rikalog')
          ),
          el(TextControl, {
            value: attributes.url,
            onChange: (val: string) => { setAttributes({ url: val }); },
            placeholder: 'https://...',
            style: { maxWidth: '400px', margin: '12px auto 0' },
          }),
          el(Button, {
            variant: 'primary',
            onClick: fetchOGP,
            disabled: !attributes.url || isLoading,
            style: { marginTop: '8px' },
          }, isLoading
            ? el(Fragment, {}, el(Spinner, null), ' ' + __('取得中...', 'rikalog'))
            : __('OGP情報を取得', 'rikalog')
          )
        )
      );
    }

    return el(Fragment, {}, sidebarPanel, preview);
  },

  save() {
    // Server-side rendering
    return null;
  },
});
