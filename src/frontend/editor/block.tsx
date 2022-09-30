// eslint-disable-next-line import/no-extraneous-dependencies
import { ReactElement } from 'react';
// eslint-disable-next-line import/no-extraneous-dependencies
import { addFilter } from '@wordpress/hooks';
// eslint-disable-next-line import/no-extraneous-dependencies
import { createHigherOrderComponent } from '@wordpress/compose';
import EmbedEdit, { EmbedEditProps } from './forked-components/EmbedEdit';
import EmbedEditControls from './forked-components/EmbedEditControls';

type BlockEditType = (props: EmbedEditProps) => ReactElement;

const lazyLoadVideosBlockEdit = createHigherOrderComponent(
  (BlockEdit: BlockEditType) => (props: EmbedEditProps) => {
    const { attributes, name } = props;

    const loadYoutube = attributes?.providerNameSlug === 'youtube' && window.llvConfig?.youtube;
    const loadVimeo = attributes?.providerNameSlug === 'vimeo' && window.llvConfig?.vimeo;

    if (name === 'core/embed' && (loadYoutube || loadVimeo)) {
      // Custom styling and loading
      return [
        <EmbedEdit key="edit" {...props} />,
        props.isSelected && <EmbedEditControls key="edit-controls" {...props} />,
      ];
    }

    // Default embed handling
    return <BlockEdit {...props} />;
  },
  'lazyLoadVideos',
);

// Priority must be above 10 so that this filter runs before the
// "core/editor/align/with-toolbar-controls" filter
// https://github.com/WordPress/gutenberg/blob/10871217bc48c4f5ded3e13a9088f6ff91da3518/packages/block-editor/src/hooks/align.js#L241
addFilter('editor.BlockEdit', 'kw/lazy-load-videos', lazyLoadVideosBlockEdit, 5);
