/**
 * Nuvora Panel Block — Gutenberg Editor Script
 *
 * @package Nuvora
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	var el              = element.createElement;
	var __              = i18n.__;
	var useBlockProps   = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var MediaUpload     = blockEditor.MediaUpload;
	var MediaUploadCheck = blockEditor.MediaUploadCheck;
	var RichText        = blockEditor.RichText;
	var PanelBody       = components.PanelBody;
	var TextControl     = components.TextControl;
	var ToggleControl   = components.ToggleControl;
	var Button          = components.Button;

	blocks.registerBlockType( 'nuvora/panel', {
		title: __( 'Nuvora Panel', 'nuvora' ),
		description: __( 'An animated article panel for the Dimension theme.', 'nuvora' ),
		category: 'nuvora-blocks',
		icon: 'welcome-widgets-menus',

		edit: function ( props ) {
			var attributes  = props.attributes;
			var setAttributes = props.setAttributes;

			var blockProps = useBlockProps( {
				className: 'nuvora-panel-editor-preview',
			} );

			return [
				el( InspectorControls, { key: 'controls' },
					el( PanelBody, { title: __( 'Panel Settings', 'nuvora' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Panel ID (anchor)', 'nuvora' ),
							help: __( 'Must match your nav item ID e.g. "intro", "work", "about"', 'nuvora' ),
							value: attributes.panelId,
							onChange: function ( val ) { setAttributes( { panelId: val.replace( /[^a-z0-9-_]/gi, '' ) } ); }
						} ),
						el( TextControl, {
							label: __( 'Panel Title', 'nuvora' ),
							value: attributes.panelTitle,
							onChange: function ( val ) { setAttributes( { panelTitle: val } ); }
						} ),
						el( 'div', { style: { marginBottom: '16px' } },
							el( 'label', { style: { display: 'block', marginBottom: '8px', fontWeight: '600' } },
								__( 'Panel Image', 'nuvora' )
							),
							el( MediaUploadCheck, null,
								el( MediaUpload, {
									onSelect: function ( media ) {
										setAttributes( {
											imageUrl: media.url,
											imageAlt: media.alt || '',
											imageId: media.id,
										} );
									},
									allowedTypes: [ 'image' ],
									value: attributes.imageId,
									render: function ( obj ) {
										return el( Button, {
											onClick: obj.open,
											className: attributes.imageUrl ? 'button' : 'button button-primary',
											style: { marginBottom: '8px' },
										}, attributes.imageUrl
											? __( 'Change Image', 'nuvora' )
											: __( 'Select Image', 'nuvora' )
										);
									}
								} )
							),
							attributes.imageUrl && el( 'div', null,
								el( 'img', { src: attributes.imageUrl, style: { maxWidth: '100%', borderRadius: '4px', marginBottom: '8px' } } ),
								el( Button, {
									isDestructive: true,
									isSmall: true,
									onClick: function () { setAttributes( { imageUrl: '', imageAlt: '', imageId: 0 } ); }
								}, __( 'Remove Image', 'nuvora' ) )
							)
						),
						el( ToggleControl, {
							label: __( 'Include Contact Form', 'nuvora' ),
							help: __( 'Add the contact form to this panel (uses Theme Options email)', 'nuvora' ),
							checked: attributes.showContact,
							onChange: function ( val ) { setAttributes( { showContact: val } ); }
						} ),
						el( ToggleControl, {
							label: __( 'Include Social Icons', 'nuvora' ),
							help: __( 'Show social icons (configured in Theme Options)', 'nuvora' ),
							checked: attributes.showSocial,
							onChange: function ( val ) { setAttributes( { showSocial: val } ); }
						} )
					)
				),

				el( 'div', blockProps,
					el( 'div', { className: 'nuvora-panel-editor-header' },
						el( 'div', { className: 'nuvora-panel-editor-id' },
							el( 'span', { className: 'dashicons dashicons-screenoptions' } ),
							el( 'strong', null, ' #' + ( attributes.panelId || 'panel-id' ) )
						),
						el( 'h2', { className: 'nuvora-panel-editor-title' },
							attributes.panelTitle || __( 'Panel Title', 'nuvora' )
						)
					),

					attributes.imageUrl && el( 'div', { className: 'nuvora-panel-editor-image' },
						el( 'img', { src: attributes.imageUrl, alt: attributes.imageAlt } )
					),

					el( RichText, {
						tagName: 'div',
						className: 'panel-content nuvora-panel-editor-content',
						value: attributes.content,
						onChange: function ( val ) { setAttributes( { content: val } ); },
						placeholder: __( 'Write your panel content here…', 'nuvora' ),
					} ),

					attributes.showContact && el( 'div', { className: 'nuvora-panel-editor-notice' },
						el( 'span', { className: 'dashicons dashicons-email-alt' } ),
						' ', __( 'Contact form will appear here on the frontend.', 'nuvora' )
					),
					attributes.showSocial && el( 'div', { className: 'nuvora-panel-editor-notice' },
						el( 'span', { className: 'dashicons dashicons-share' } ),
						' ', __( 'Social icons will appear here on the frontend.', 'nuvora' )
					)
				)
			];
		},

		save: function ( props ) {
			// Server-side rendered — save returns null
			return null;
		}
	} );

} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n
);
