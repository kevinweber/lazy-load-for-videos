// eslint-disable-next-line import/no-extraneous-dependencies
import { addFilter } from '@wordpress/hooks';
// eslint-disable-next-line import/no-extraneous-dependencies
import { createHigherOrderComponent } from '@wordpress/compose';
import EmbedEdit, { EmbedEditProps } from './EmbedEdit';
import EmbedEditControls from './EmbedEditControls';

type BlockEdit = (props: EmbedEditProps) => React.ReactElement;

const lazyLoadVideosBlockEdit = createHigherOrderComponent(
  (BlockEdit: BlockEdit) => (props: EmbedEditProps) => {
    const { name } = props;
    const loadYoutube = name === 'core-embed/youtube' && window.llvConfig?.youtube;
    const loadVimeo = name === 'core-embed/vimeo' && window.llvConfig?.vimeo;
    if (loadYoutube || loadVimeo) {
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
