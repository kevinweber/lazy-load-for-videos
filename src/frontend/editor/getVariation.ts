// @ts-expect-error
// eslint-disable-next-line import/no-extraneous-dependencies
import { __, _x } from '@wordpress/i18n';
import { embedContentIcon, embedYouTubeIcon, embedVimeoIcon } from './icons';
import lazyloadYoutube from '../lazyload-youtube/lazyloadYoutube';
import lazyloadVimeo from '../lazyload-vimeo/lazyloadVimeo';

declare global {
  interface Window {
    llvConfig?: {
      youtube?: any;
      vimeo?: any;
    };
  }
}

export type ProviderName = 'youtube' | 'vimeo';
export type RefProps = HTMLElement | null;

const variations = {
  youtube: {
    title: 'YouTube',
    icon: embedYouTubeIcon,
    keywords: [__('music'), __('video')],
    description: __('Embed a YouTube video.'),
    patterns: [
      /^https?:\/\/((m|www)\.)?youtube\.com\/.+/i,
      /^https?:\/\/youtu\.be\/.+/i,
    ],
    attributes: { providerNameSlug: 'youtube', responsive: true },
    init: (rootNode: RefProps) => {
      if (!window?.llvConfig?.youtube) return;
      lazyloadYoutube({
        ...window.llvConfig.youtube,
        rootNode,
      });
    },
  },
  vimeo: {
    title: 'Vimeo',
    icon: embedVimeoIcon,
    keywords: [__('video')],
    description: __('Embed a Vimeo video.'),
    patterns: [/^https?:\/\/(www\.)?vimeo\.com\/.+/i],
    attributes: { providerNameSlug: 'vimeo', responsive: true },
    init: (rootNode: RefProps) => {
      if (!window?.llvConfig?.vimeo) return;
      lazyloadVimeo({
        ...window.llvConfig.vimeo,
        rootNode,
      });
    },
  },
  default: {
    title: _x('Embed', 'block title'),
    icon: embedContentIcon,
    init: () => { },
  },
};

export default function getVariation(provider: ProviderName) {
  return variations[provider] || variations.default;
}
