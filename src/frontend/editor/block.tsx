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
      return (
        <>
          <EmbedEdit {...props} />
          {props.isSelected && <EmbedEditControls {...props} />}
        </>
      );
    }
    // Default embed handling
    return <BlockEdit {...props} />;
  },
  'lazyLoadVideos',
);

addFilter('editor.BlockEdit', 'kw/lazy-load-videos', lazyLoadVideosBlockEdit);
