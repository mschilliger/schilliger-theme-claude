(function (wp) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var RichText = wp.blockEditor.RichText;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;
	var Spinner = wp.components.Spinner;
	var apiFetch = wp.apiFetch;
	var __ = wp.i18n.__;

	function fetchPreview(url) {
		return apiFetch({ path: '/schilliger/v1/link-preview?url=' + encodeURIComponent(url) });
	}

	registerBlockType('schilliger/link-list-item', {
		edit: function (props) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var state = useState(false);
			var loading = state[0];
			var setLoading = state[1];
			var blockProps = useBlockProps({ className: 'schilliger-link-list-item' });
			var hasContent = Boolean(attributes.title || attributes.image);

			function doFetch(url) {
				if (!url) {
					return;
				}
				setLoading(true);
				fetchPreview(url)
					.then(function (data) {
						setAttributes({
							title: data.title || '',
							image: data.image || '',
							siteName: data.siteName || '',
						});
					})
					.catch(function () {})
					.finally(function () {
						setLoading(false);
					});
			}

			if (!hasContent) {
				return el(
					'div',
					{ className: 'schilliger-link-list-item-setup' },
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
			}

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'schilliger-link-list-item-media' },
					attributes.image ? el('img', { src: attributes.image, alt: '' }) : null
				),
				el(
					'div',
					{ className: 'schilliger-link-list-item-body' },
					el(TextControl, {
						label: __('Titel', 'schilliger'),
						value: attributes.title,
						onChange: function (value) {
							setAttributes({ title: value });
						},
					}),
					el(RichText, {
						tagName: 'p',
						className: 'schilliger-link-list-item-comment',
						placeholder: __('Dein Kommentar zu diesem Link ...', 'schilliger'),
						value: attributes.comment,
						onChange: function (value) {
							setAttributes({ comment: value });
						},
					}),
					el(TextControl, {
						label: __('URL', 'schilliger'),
						value: attributes.url,
						onChange: function (value) {
							setAttributes({ url: value });
						},
					}),
					el(
						Button,
						{
							variant: 'link',
							onClick: function () {
								doFetch(attributes.url);
							},
							disabled: !attributes.url || loading,
						},
						loading ? el(Spinner, {}) : __('Vorschau neu laden', 'schilliger')
					)
				)
			);
		},
		save: function (props) {
			var attributes = props.attributes;
			var blockProps = wp.blockEditor.useBlockProps.save({ className: 'schilliger-link-list-item' });
			var linkProps = Object.assign({}, blockProps, {
				href: attributes.url,
				target: '_blank',
				rel: 'noopener noreferrer',
			});

			var mediaChild = el(
				'div',
				{ className: 'schilliger-link-list-item-media' },
				attributes.image ? el('img', { src: attributes.image, alt: '' }) : null
			);

			var bodyChildren = [
				el('span', { className: 'schilliger-link-list-item-title', key: 'title' }, attributes.title),
				el(RichText.Content, {
					tagName: 'p',
					className: 'schilliger-link-list-item-comment',
					value: attributes.comment,
					key: 'comment',
				}),
				el('span', { className: 'schilliger-link-list-item-url', key: 'url' }, attributes.url),
			];

			return el('a', linkProps, mediaChild, el('div', { className: 'schilliger-link-list-item-body' }, bodyChildren));
		},
	});
})(window.wp);
