/**
 * WordPress JavaScript API type definitions
 */

declare namespace wp {
  namespace blocks {
    interface BlockConfiguration {
      title: string;
      description?: string;
      icon: any;
      category: string;
      keywords?: string[];
      attributes: Record<string, { type: string; default?: any }>;
      supports?: Record<string, boolean>;
      edit: (props: BlockEditProps) => any;
      save: (props: any) => any;
    }

    interface BlockEditProps {
      attributes: Record<string, any>;
      setAttributes: (attrs: Record<string, any>) => void;
      className?: string;
      isSelected?: boolean;
    }

    function registerBlockType(name: string, config: BlockConfiguration): void;
  }

  namespace element {
    function createElement(type: any, props?: any, ...children: any[]): any;
    function useState<T>(initial: T): [T, (val: T | ((prev: T) => T)) => void];
    function useRef<T>(initial: T): { current: T };
    function useEffect(effect: () => void | (() => void), deps?: any[]): void;
    const Fragment: any;
  }

  namespace blockEditor {
    const InspectorControls: any;
    const RichTextToolbarButton: any;
  }

  namespace components {
    const PanelBody: any;
    const TextControl: any;
    const ToggleControl: any;
    const Button: any;
    const Spinner: any;
  }

  namespace i18n {
    function __(text: string, domain?: string): string;
  }

  namespace richText {
    interface RichTextValue {
      activeFormats?: Array<{
        type: string;
        attributes?: Record<string, string>;
      }>;
    }

    interface FormatConfig {
      title: string;
      tagName: string;
      className: string;
      attributes?: Record<string, string>;
      edit: (props: any) => any;
    }

    function registerFormatType(name: string, config: FormatConfig): void;
    function applyFormat(value: RichTextValue, format: { type: string; attributes?: Record<string, string> }): RichTextValue;
    function removeFormat(value: RichTextValue, formatType: string): RichTextValue;
  }

  namespace apiFetch {
    interface Options {
      path: string;
      method?: string;
      data?: any;
    }
  }

  function apiFetch(options: apiFetch.Options): Promise<any>;
}

interface Window {
  wp: typeof wp;
}
