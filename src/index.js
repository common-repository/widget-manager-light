'use strict';

const addWidgetVisibility = wp.compose.createHigherOrderComponent((BlockEdit) => {
	
	return (props) => {
		const { Fragment } = wp.element;
		const { ToggleControl } = wp.components;
		const { InspectorAdvancedControls } = wp.blockEditor;
		const { attributes, setAttributes, isSelected } = props;
		
		return (
			<Fragment>
				<BlockEdit {...props} />
				{isSelected  && typeof( attributes.__internalWidgetId ) != 'undefined' &&
					<InspectorAdvancedControls>
						<input type="button" href="javascript:;" class="button otw_appearence" onClick={ () => setVisibilityClick( attributes.__internalWidgetId )} data-sidebar={attributes.__internalWidgetId} value={wp.i18n.__('Set Visibility', 'otw-sbm')}/>
					</InspectorAdvancedControls>
				}
			</Fragment>
		);
	}
}, 'addWidgetVisibility' );

function setVisibilityClick( widget_id, param ){
	otw_load_visibility_dialog( widget_id, '' );
}

wp.hooks.addFilter( 'editor.BlockEdit', 'otw_wml', addWidgetVisibility );