(function (wp) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;
	var Spinner = wp.components.Spinner;
	var Notice = wp.components.Notice;
	var apiFetch = wp.apiFetch;
	var __ = wp.i18n.__;

	function fetchPreview(url) {
		return apiFetch({ path: '/schilliger/v1/link-preview?url=' + encodeURIComponent(url) });
	}

	function PreviewCard(attributes) {
		var children = [];

		if (attributes.image) {
			children.push(
				el(
					'div',
					{ className: 'schilliger-link-preview-media', key: 'media' },
					el('img', { src: attributes.image, alt: '' })
				)
			);
		}

		var bodyChildren = [
			el(
				'span',
				{ className: 'schilliger-link-preview-title', key: 'title' },
				attributes.title || __('(Titel folgt)', 'schilliger')
			),
			el('span', { className: 'schilliger-link-preview-url', key: 'url' }, attributes.url),
		];

		children.push(el('div', { className: 'schilliger-link-preview-body', key: 'body' }, bodyChildren));

		return el('div', { className: 'schilliger-link-preview size-' + attributes.size }, children);
	}

	registerBlockType('schilliger/link-preview', {
		edit: function (props) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var state = useState(false);
			var loading = state[0];
			var setLoading = state[1];
			var errorState = useState('');
			var loadError = errorState[0];
			var setLoadError = errorState[1];
			var blockProps = useBlockProps();

			var hasContent = Boolean(attributes.title || attributes.image);

			function doFetch(url) {
				if (!url) {
					return;
				}
				setLoading(true);
				setLoadError('');
				fetchPreview(url)
					.then(function (data) {
						setAttributes({
							title: data.title || '',
							image: data.image || '',
						});
						if (!data.title && !data.image) {
							setLoadError(__('Konnte keine Vorschau-Daten finden. Titel/Bild kannst du unten manuell eintragen.', 'schilliger'));
						}
					})
					.catch(function () {
						setLoadError(__('Vorschau konnte nicht geladen werden. Bitte URL pruefen oder Felder manuell ausfuellen.', 'schilliger'));
					})
					.finally(function () {
						setLoading(false);
					});
			}

			var mainContent = hasContent
				? PreviewCard(attributes)
				: el(
						'div',
						{ className: 'schilliger-link-preview-setup' },
						el(TextControl, {
							label: __('Link-URL', 'schilliger'),
							value: attributes.url,
							placeholder: 'https://...',
							onChange: function (value) {
								setAttributes({ url: value });
							},
						}),
						el(
							Button,
							{
								variant: 'primary',
								onClick: function () {
									doFetch(attributes.url);
								},
								disabled: !attributes.url || loading,
							},
							loading ? el(Spinner, {}) : __('Vorschau laden', 'schilliger')
						)
				  );

			return el(
				Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __('Link-Vorschau', 'schilliger') },
						el(TextControl, {
							label: __('URL', 'schilliger'),
							value: attributes.url,
							onChange: function (value) {
								setAttributes({ url: value });
							},
						}),
						el(SelectControl, {
							label: __('Groesse', 'schilliger'),
							value: attributes.size,
							options: [
								{ label: __('Klein', 'schilliger'), value: 'small' },
								{ label: __('Mittel', 'schilliger'), value: 'medium' },
								{ label: __('Gross', 'schilliger'), value: 'large' },
							],
							onChange: function (value) {
								setAttributes({ size: value });
							},
						}),
						el(TextControl, {
							label: __('Titel', 'schilliger'),
							value: attributes.title,
							onChange: function (value) {
								setAttributes({ title: value });
							},
						}),
						el(TextControl, {
							label: __('Bild-URL', 'schilliger'),
							value: attributes.image,
							onChange: function (value) {
								setAttributes({ image: value });
							},
						}),
						el(
							Button,
							{
								variant: 'secondary',
								onClick: function () {
									doFetch(attributes.url);
								},
								disabled: !attributes.url || loading,
							},
							loading ? el(Spinner, {}) : __('Vorschau (neu) laden', 'schilliger')
						)
					)
				),
				el(
					'div',
					blockProps,
					loadError ? el(Notice, { status: 'warning', isDismissible: false }, loadError) : null,
					mainContent
				)
			);
		},
		save: function (props) {
			var attributes = props.attributes;
			var blockProps = wp.blockEditor.useBlockProps.save();
			var className = (blockProps.className ? blockProps.className + ' ' : '') + 'schilliger-link-preview-link';
			var linkProps = Object.assign({}, blockProps, {
				href: attributes.url,
				className: className,
				target: '_blank',
				rel: 'noopener noreferrer',
			});
			return el('a', linkProps, PreviewCard(attributes));
		},
	});
})(window.wp);
