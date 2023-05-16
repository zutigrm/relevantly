import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { sectionTitle, numberOfRecommendations } = attributes;
  const blockProps = useBlockProps();

  const updateNumberOfRecommendations = (value) => {
    setAttributes({ numberOfRecommendations: value });
  };

  return (
    <div {...blockProps}>
        <RichText
            label={__('Related Content Title', 'relevantly')}
            tagName="h3"
            value={sectionTitle}
            onChange={(value) => setAttributes({ sectionTitle: value })}
            placeholder={__('Enter section title...', 'relevantly')}
        />
      <InspectorControls>
        <PanelBody title={__('Settings', 'relevantly')} initialOpen={true}>
          <RangeControl
            label={__('Number of Recommendations', 'relevantly')}
            value={numberOfRecommendations}
            onChange={updateNumberOfRecommendations}
            min={1}
            max={10}
          />
        </PanelBody>
      </InspectorControls>
    </div>
  );
}
