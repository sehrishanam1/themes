/**
 * Nuvora Hero Block — Editor Script
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	var el = element.createElement;
	var __ = i18n.__;
	var useBlockProps = blockEditor.useBlockProps;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;

	blocks.registerBlockType( 'nuvora/hero', {
		title: __( 'Nuvora Hero Header', 'nuvora' ),
		category: 'nuvora-blocks',
		icon: 'star-filled',

		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( { style: { background: '#1b1f22', padding: '2em', borderRadius: '6px', color: '#fff', textAlign: 'center' } } );

			return [
				el( InspectorControls, { key: 'controls' },
					el( PanelBody, { title: __( 'Hero Settings', 'nuvora' ), initialOpen: true },
						el( 'p', { style: { color: '#aaa', fontSize: '12px' } },
							__( 'These override Theme Options for this page only.', 'nuvora' )
						),
						el( TextControl, {
							label: __( 'Site Title Override', 'nuvora' ),
							value: attributes.siteTitle,
							onChange: function ( val ) { setAttributes( { siteTitle: val } ); },
							placeholder: __( 'Leave blank to use Theme Options title', 'nuvora' )
						} ),
						el( TextareaControl, {
							label: __( 'Subtitle Override', 'nuvora' ),
							value: attributes.subtitle,
							onChange: function ( val ) { setAttributes( { subtitle: val } ); },
							placeholder: __( 'Leave blank to use Theme Options subtitle', 'nuvora' )
						} ),
						el( TextControl, {
							label: __( 'Logo Icon Class', 'nuvora' ),
							value: attributes.logoIcon,
							onChange: function ( val ) { setAttributes( { logoIcon: val } ); },
							help: __( 'FontAwesome class e.g. fa-gem, fa-rocket, fa-star', 'nuvora' )
						} )
					)
				),
				el( 'div', blockProps,
					el( 'div', { style: { background: 'rgba(255,255,255,0.05)', borderRadius: '50%', width: '5.375em', height: '5.375em', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 1em', fontSize: '18px' } },
						el( 'span', { className: 'icon ' + ( attributes.logoIcon || 'fa-gem' ), style: { fontSize: '2em' } } )
					),
					el( 'h1', { style: { color: '#fff', margin: '0 0 0.5em', fontSize: '1.5em', letterSpacing: '0.15em', textTransform: 'uppercase' } },
						attributes.siteTitle || __( '(Title from Theme Options)', 'nuvora' )
					),
					el( 'p', { style: { color: 'rgba(255,255,255,0.6)', margin: '0', fontSize: '0.9em' } },
						attributes.subtitle || __( '(Subtitle from Theme Options)', 'nuvora' )
					),
					el( 'div', { style: { marginTop: '1em', opacity: 0.5, fontSize: '12px' } },
						__( '↑ Hero Header — Nav links are managed in Theme Options', 'nuvora' )
					)
				)
			];
		},

		save: function () { return null; }
	} );

} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n
);
