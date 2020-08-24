import classnames from 'classnames';
import {
  createUpgradedEmbedBlock,
  getClassNames,
  fallback,
  getAttributesFromPreview,
} from '@wordpress/block-library/src/embed/util';
import EmbedControls from '@wordpress/block-library/src/embed/embed-controls';
import EmbedPreview from '@wordpress/block-library/src/embed/embed-preview';
import EmbedLoading from '@wordpress/block-library/src/embed/embed-loading';
import EmbedPlaceholder from '@wordpress/block-library/src/embed/embed-placeholder';
// @ts-expect-error
// eslint-disable-next-line import/no-extraneous-dependencies
import { __, sprintf } from '@wordpress/i18n';
import {
  useState, useEffect, useRef, useCallback,
  // @ts-expect-error
  // eslint-disable-next-line import/no-extraneous-dependencies
} from '@wordpress/element';
// eslint-disable-next-line import/no-extraneous-dependencies
import { useDispatch, useSelect } from '@wordpress/data';
import getVariation, { ProviderName, RefProps } from './getVariation';

export type EmbedEditProps = {
  attributes: {
    providerNameSlug: ProviderName;
    previewable: boolean;
    responsive: boolean;
    url: string;
    allowResponsive: boolean;
    className?: string;
  };
  name: string;
  className?: string;
  isSelected: boolean;
  onReplace: Function;
  setAttributes: Function;
  insertBlocksAfter: Function;
};

/** Based on https://github.com/WordPress/gutenberg/blob/02590029e7464efafee46141b3549763dbf22cd4/packages/block-library/src/embed/edit.js */
export default function EmbedEdit(props: EmbedEditProps) {
  const {
    attributes,
    isSelected,
    onReplace,
    setAttributes,
    insertBlocksAfter,
  } = props;
  const {
    providerNameSlug,
    responsive,
    url: attributesUrl,
    allowResponsive,
    className,
  } = attributes;

  const { icon, title, init } = getVariation(providerNameSlug);
  const [url, setURL] = useState(attributesUrl);
  const [isEditingURL, setIsEditingURL] = useState(false);
  const { invalidateResolution } = useDispatch('core/data');
  const [domNode, setDomNode] = useState(null);
  const blockRef = (node: RefProps) => setDomNode(node);

  useEffect(() => {
    if (!domNode) return;
    init(domNode);
  }, [domNode, init]);

  const {
    preview, fetching, themeSupportsResponsive, cannotEmbed,
  } = useSelect(
    (select) => {
      const {
        getEmbedPreview,
        isPreviewEmbedFallback,
        isRequestingEmbedPreview,
        getThemeSupports,
      } = select('core');
      if (!attributesUrl) {
        return { fetching: false, cannotEmbed: false };
      }

      const embedPreview:
        | { data?: { status: number }; html?: string }
        | undefined = getEmbedPreview(attributesUrl);
      const previewIsFallback = isPreviewEmbedFallback(attributesUrl);

      // Some WordPress URLs that can't be embedded will cause the API to return
      // a valid JSON response with no HTML and `data.status` set to 404, rather
      // than generating a fallback response as other embeds do.
      const wordpressCantEmbed = embedPreview?.data?.status === 404;
      const validPreview = !!embedPreview && !wordpressCantEmbed;
      return {
        preview: validPreview ? embedPreview : undefined,
        fetching: isRequestingEmbedPreview(attributesUrl),
        // @ts-expect-error
        themeSupportsResponsive: getThemeSupports()['responsive-embeds'],
        cannotEmbed: !validPreview || previewIsFallback,
      };
    },
    [attributesUrl],
  );

  /**
   * @return {Object} Attributes derived from the preview, merged with the current attributes.
   */
  const getMergedAttributes = useCallback(() => ({
    ...attributes,
    ...getAttributesFromPreview(
      preview,
      title,
      className,
      responsive,
      allowResponsive,
    ),
  }), [allowResponsive, attributes, className, preview, responsive, title]);

  const toggleResponsive = () => {
    const { html } = preview;
    const newAllowResponsive = !allowResponsive;

    setAttributes({
      allowResponsive: newAllowResponsive,
      className: getClassNames(
        html,
        className,
        responsive && newAllowResponsive,
      ),
    });
  };

  useEffect(() => {
    if (!preview?.html || !cannotEmbed || fetching) {
      return;
    }
    // At this stage, we're not fetching the preview and know it can't be embedded,
    // so try removing any trailing slash, and resubmit.
    const newURL = attributesUrl.replace(/\/$/, '');
    setURL(newURL);
    setIsEditingURL(false);
    setAttributes({ url: newURL });
  }, [preview?.html, attributesUrl, cannotEmbed, fetching, setAttributes]);

  // Handle incoming preview
  useEffect(() => {
    if (preview && !isEditingURL) {
      // Even though we set attributes that get derived from the preview,
      // we don't access them directly because for the initial render,
      // the `setAttributes` call will not have taken effect. If we're
      // rendering responsive content, setting the responsive classes
      // after the preview has been rendered can result in unwanted
      // clipping or scrollbars. The `getAttributesFromPreview` function
      // that `getMergedAttributes` uses is memoized so that we're not
      // calculating them on every render.
      setAttributes(getMergedAttributes());
      if (onReplace) {
        const upgradedBlock = createUpgradedEmbedBlock(
          props,
          getMergedAttributes(),
        );

        if (upgradedBlock) {
          onReplace(upgradedBlock);
        }
      }
    }
  }, [preview, isEditingURL, getMergedAttributes, onReplace, props, setAttributes]);

  if (fetching) {
    return <EmbedLoading />;
  }

  // translators: %s: type of embed e.g: "YouTube", "Twitter", etc. "Embed" is used
  // when no specific type exists
  const label = sprintf(__('%s URL'), title);

  // No preview, or we can't embed the current URL, or we've clicked the edit button.
  const showEmbedPlaceholder = !preview || cannotEmbed || isEditingURL;
  if (showEmbedPlaceholder) {
    return (
      <EmbedPlaceholder
        icon={icon}
        label={label}
        onSubmit={(event: InputEvent) => {
          if (event) {
            event.preventDefault();
          }

          setIsEditingURL(false);
          setAttributes({ url });
        }}
        value={url}
        cannotEmbed={cannotEmbed}
        onChange={(event: InputEvent) => setURL((event.target as HTMLTextAreaElement).value)
        }
        fallback={() => fallback(url, onReplace)}
        tryAgain={() => {
          invalidateResolution('core', 'getEmbedPreview', [url]);
        }}
      />
    );
  }

  // Even though we set attributes that get derived from the preview,
  // we don't access them directly because for the initial render,
  // the `setAttributes` call will not have taken effect. If we're
  // rendering responsive content, setting the responsive classes
  // after the preview has been rendered can result in unwanted
  // clipping or scrollbars. The `getAttributesFromPreview` function
  // that `getMergedAttributes` uses is memoized so that we're not
  const {
    caption,
    allowResponsive: allowResponsiveFromPreview,
    className: classFromPreview,
  } = getMergedAttributes();
  const combinedClassName = classnames(classFromPreview, props.className, !isSelected && 'lazy-load-block-play');

  return (
    <div ref={blockRef}>
      <EmbedControls
        showEditButton={preview && !cannotEmbed}
        themeSupportsResponsive={themeSupportsResponsive}
        blockSupportsResponsive={responsive}
        allowResponsive={allowResponsiveFromPreview}
        toggleResponsive={toggleResponsive}
        switchBackToURLInput={() => setIsEditingURL(true)}
      />
      {/* @ts-expect-error */}
      <EmbedPreview
        // preview={preview.html && <WpEmbedPreview html={preview.html} />}
        preview={preview}
        // This type is important: Setting it to "wp-embed" makes the preview component render
        // the HTML directly instead of in an iframe
        type="wp-embed"
        // Manually set "previewable" because otherwise the value isn't
        previewable
        className={combinedClassName}
        url={url}
        caption={caption}
        onCaptionChange={(value: InputEvent) => setAttributes({ caption: value })}
        isSelected={isSelected}
        icon={icon}
        label={label}
        insertBlocksAfter={insertBlocksAfter}
      />
    </div>
  );
}
