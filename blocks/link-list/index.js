(function (wp) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;

	var ALLOWED_BLOCKS = ['schilliger/link-list-item'];
	var TEMPLATE = [['schilliger/link-list-item', {}]];

	registerBlockType('schilliger/link-list', {
		edit: function () {
			var blockProps = useBlockProps({ className: 'schilliger-link-list' });
			var innerBlocksProps = useInnerBlocksProps(blockProps, {
				allowedBlocks: ALLOWED_BLOCKS,
				template: TEMPLATE,
				templateLock: false,
			});
			return el('div', innerBlocksProps);
		},
		save: function () {
			var blockProps = wp.blockEditor.useBlockProps.save({ className: 'schilliger-link-list' });
			var innerBlocksProps = wp.blockEditor.useInnerBlocksProps.save(blockProps);
			return el('div', innerBlocksProps);
		},
	});
})(window.wp);
