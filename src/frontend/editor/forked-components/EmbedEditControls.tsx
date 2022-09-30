// eslint-disable-next-line import/no-extraneous-dependencies
import { TextControl } from '@wordpress/components';
// @ts-expect-error
// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from '@wordpress/i18n';
// @ts-expect-error
// https://developer.wordpress.org/block-editor/packages/packages-block-editor/
// eslint-disable-next-line import/no-extraneous-dependencies
import { InspectorAdvancedControls } from '@wordpress/block-editor';
import { EmbedEditProps } from './EmbedEdit';

export default function EmbedEditControls({ attributes, setAttributes }: EmbedEditProps) {
  return (
    // https://github.com/WordPress/gutenberg/blob/master/packages/block-editor/src/components/inspector-controls/README.md
    <InspectorAdvancedControls>
      {/* Based on https://github.com/WordPress/gutenberg/blob/4c535288a6a2b75ff23ee96c75f7d9877e919241/packages/block-editor/src/hooks/custom-class-name.js#L68 */}
      <TextControl
        autoComplete="off"
        label={__('Additional CSS class(es)')}
        value={attributes.className || ''}
        onChange={(nextValue: string) => {
          setAttributes({
            className:
              nextValue !== '' ?
                nextValue :
                undefined,
          });
        }}
        help={__(
          'Separate multiple classes with spaces.',
        )}
      />
    </InspectorAdvancedControls>
  );
}
