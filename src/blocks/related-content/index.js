import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import edit from './edit';
import save from './save';

registerBlockType('relevantly/related-content', {
    title: __('Related Content', 'relevantly'),
    description: __('Display related content based on the post content.', 'relevantly'),
    category: 'widgets',
    icon: 'list-view',
    keywords: [
        __('related content', 'relevantly'), 
        __('content recommendation', 'relevantly')],
    supports: {
        html: false,
    },
    attributes: {
        numberOfRecommendations: {
            type: 'number',
            default: 2,
        },
        sectionTitle: {
            type: 'string',
            default: '',
        },
    },
    edit,
    save,
});