(function (wp) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var useBlockProps = wp.blockEditor.useBlockProps;

	var ALLOWED_BLOCKS = ['schilliger/link-list-item'];
	var TEMPLATE = [['schilliger/link-list-item', {}]];

	registerBlockType('schilliger/link-list', {
		edit: function () {
			var blockProps = useBlockProps({ className: 'schilliger-link-list' });
			return el(
				'div',
				blockProps,
				el(InnerBlocks, {
					allowedBlocks: ALLOWED_BLOCKS,
					template: TEMPLATE,
					templateLock: false,
				})
			);
		},
		save: function () {
			var blockProps = wp.blockEditor.useBlockProps.save({ className: 'schilliger-link-list' });
			return el('div', blockProps, el(InnerBlocks.Content, {}));
		},
	});
})(window.wp);
