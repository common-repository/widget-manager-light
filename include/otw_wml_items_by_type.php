<?php
/** OTW Sidebar & Widget Manager Column Interface
  *  load wp items by given type and string
  */
global $wp_registered_sidebars, $wp_wml_int_items, $otw_wml_plugin_url;

$otw_options = get_option( 'otw_plugin_options' );

$items_limit = 20;

if( isset( $otw_options['otw_sbm_items_limit'] ) && intval( $otw_options['otw_sbm_items_limit'] ) ){
	$items_limit = $otw_options['otw_sbm_items_limit'];
}


$wp_item_type = '';
$otw_sidebar_id = 0;
$widget = '';
$string_filter = '';
$format = '';
$order  = 'a_z';
$show   = 'all';
$current_page = 0;
if( otw_post( 'type', false ) )
{
	$wp_item_type = otw_post( 'type', '' );
}
if( otw_post( 'sidebar', false ) && strlen( trim( otw_post( 'sidebar', '' ) ) ) )
{
	$otw_sidebar_id = otw_post( 'sidebar', '' );
}
if( otw_post( 'string_filter', false ) )
{
	$string_filter = otw_post( 'string_filter', '' );
}
if( otw_post( 'format', false ) )
{
	$format = otw_post( 'format', '' );
}
if( otw_post( 'widget', false ) )
{
	$widget = otw_post( 'widget', '' );
}
if( otw_post( 'order', false ) )
{
	$order = otw_post( 'order', '' );
}
if( otw_post( 'show', false ) )
{
	$show = otw_post( 'show', '' );
}
if( otw_post( 'per_page', false ) )
{
	$items_limit = otw_post( 'per_page', '' );
}
if( otw_post( 'page', false ) )
{
	$current_page = otw_post( 'page', '' );
}
$otw_sidebar_values = array(
	'sbm_title'              =>  '',
	'sbm_description'        =>  '',
	'sbm_replace'            =>  '',
	'sbm_status'             =>  'inactive',
	'sbm_widget_alignment'   =>  'vertical'
);

if( $format == 'ids' ){
	$db_items = otw_wml_get_filtered_items( $wp_item_type, $string_filter, $otw_sidebar_id, 0 );
	
	$items = array();
	$total_items = 0;
	
	if( isset( $db_items[1] ) )
	{
		$total_items = $db_items[0];
		$items = $db_items[1];
	}
	
	$keys = array();
	foreach( $items as $wpItem ){
		$key = otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem );
		$keys[ $key ] = $key;
	}
	if( count( $keys ) ){
		echo implode( ",", $keys );
	}
	die;
}elseif( $format == 'a_dialog' ){
	$otw_sidebars = get_option( 'otw_sidebars' );
	
	if( !is_array( $otw_sidebars ) ){
		$otw_sidebars = array();
	}
	
	$id_in_list = array();
	$id_not_in_list = array();
	if( isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] ) && !isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ]['all'] ) )
	{
		if( count( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] ) )
		{
			$id_in_list = array_keys( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] );
			$id_in_list = array_combine( $id_in_list, $id_in_list );
		}
	}
	$otw_widget_settings = get_option( 'otw_widget_settings' );
	
	switch( $show )
	{
		case 'all_unselected':
				
				if( isset( $otw_widget_settings[ $otw_sidebar_id ] ) && isset( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ] ) ){
					
					$filtered = false;
					if( isset( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'] ) && isset( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'][$widget] ) ){
						
						if( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'][$widget] == 'vis' ){
							$id_in_list = array( 'otw_0_0' => 'otw_0_0' );
							$filtered = true;
						}elseif( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'][$widget] == 'invis' ){
							$filtered = true;
						}
					}
					
					if( !$filtered ){
						
						$initial_in_list = $id_in_list;
						
						if( count( $id_in_list ) ){
							
							foreach( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ] as $item_type_id => $item_widget_data ){
								
								if( $item_type_id == '_otw_wc' ){
									continue;
								}
								if( count( $id_in_list ) && !in_array( $item_type_id, $initial_in_list ) ){
									continue;
								}
								
								if( !isset( $item_widget_data['exclude_widgets'] ) || !isset( $item_widget_data['exclude_widgets'][ $widget ] ) ){
									$id_not_in_list[] = $item_type_id;
									
									if( isset( $id_in_list[ $item_type_id ] ) ){
										unset( $id_in_list[ $item_type_id ] );
									}
								}
							}
						}else{
							foreach( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ] as $item_type_id => $item_widget_data ){
								
								if( $item_type_id == '_otw_wc' ){
									continue;
								}
								
								if( isset( $item_widget_data['exclude_widgets'] ) && isset( $item_widget_data['exclude_widgets'][ $widget ] ) ){
								
									$id_in_list[] = $item_type_id;
								}
							}
							if( !count( $id_in_list ) ){
								$id_in_list[] = 'otw_0_0';
							}
						}
					}
					
				}
			break;
		case 'all_selected':
				
				if( isset( $otw_widget_settings[ $otw_sidebar_id ] ) && isset( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ] ) ){
					
					$filtered = false;
					
					if( isset( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'] ) && isset( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'][$widget] ) ){
						
						if( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'][$widget] == 'vis' ){
							$fitered = true;
						}elseif( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ]['_otw_wc'][$widget] == 'invis' ){
							$filtred = true;
							$id_in_list = array( 'otw_0_0' => 'otw_0_0' );
						}
					}
					
					if( !$filtered ){
						
						foreach( $otw_widget_settings[ $otw_sidebar_id ][ $wp_item_type ] as $item_type_id => $item_widget_data ){
							
							if( $item_type_id == '_otw_wc' ){
								continue;
							}
							if( count( $id_in_list ) && !in_array( $item_type_id, $id_in_list ) ){
								continue;
							}
							
							if( isset( $item_widget_data['exclude_widgets'] ) && isset( $item_widget_data['exclude_widgets'][ $widget ] ) ){
								
								$id_not_in_list[] = $item_type_id;
								
								if( isset( $id_in_list[ $item_type_id ] ) ){
									unset( $id_in_list[ $item_type_id ] );
								}
							}
						}
					}
				}
			break;
	}
	$db_items = otw_wml_get_filtered_items( $wp_item_type, $string_filter, $otw_sidebar_id, $items_limit, $id_in_list, $id_not_in_list, $show, $order, $current_page );
	
	$items = array();
	$total_items = 0;
	$total_valid = 0;
	$total_selected = 0;
	
	if( isset( $db_items[1] ) )
	{
		$total_items = $db_items[0];
		$items = $db_items[1];
		$totals = otw_wml_get_total_not_excluded( $otw_sidebar_id, $widget, $wp_item_type );
		$total_valid = $totals[0];
		$total_selected = $totals[1];
		$pager_data = $db_items[2];
		if( isset( $pager_data['current'] ) ){
			$current_page = $pager_data['current'];
		}
	}
}else{
	$id_in_list = array();
	$id_not_in_list = array();
	
	switch( $show )
	{
		case 'all_selected':
				$otw_sidebars = get_option( 'otw_sidebars' );
				if( isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] ) && !isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ]['all'] ) ){
					$id_in_list = array_keys( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] );
				}elseif( isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] ) && isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ]['all'] ) ){
					$id_in_list = array_keys( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] );
				}
				if( !count( $id_in_list ) ){
					$id_in_list[] = 'otw_0_0';
				}
			break;
		case 'all_unselected':
				$otw_sidebars = get_option( 'otw_sidebars' );
				if( isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] ) && !isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ]['all'] ) ){
					$id_not_in_list = array_keys( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] );
				}elseif( isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] ) && isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ]['all'] ) ){
					$id_not_in_list = array_keys( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ] );
				}
			break;
	}
	$db_items = otw_wml_get_filtered_items( $wp_item_type, $string_filter, $otw_sidebar_id, $items_limit, $id_in_list, $id_not_in_list, $show, $order, $current_page );
	
	$items = array();
	$total_items = 0;
	
	if( isset( $db_items[1] ) )
	{
		$total_items = $db_items[0];
		$items = $db_items[1];
		$pager_data = $db_items[2];
		
		if( isset( $pager_data['current'] ) ){
			$current_page = $pager_data['current'];
		}
	}
}
$wp_item_data = $wp_wml_int_items[$wp_item_type];
?>
<?php if( $format == 'a_dialog'){?>
	<?php if( is_array( $items ) && count( $items ) ){?>
		
		<?php foreach( $items as $wpItem ) {?>
			<?php if( isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ][ otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ) ] ) || isset( $wp_registered_sidebars[ $otw_sidebar_id ]['validfor'][ $wp_item_type ][ 'all' ] ) || !array_key_exists( $otw_sidebar_id, $otw_sidebars ) || !$wp_registered_sidebars[ $otw_sidebar_id ]['replace']  ){?>
				<p<?php otw_sidebar_item_row_attributes( 'p', $wp_item_type, $otw_sidebar_id, $widget, $wpItem )?> >
					<a href="javascript:;"<?php otw_sidebar_item_row_attributes( 'a', $wp_item_type, $otw_sidebar_id, $widget, $wpItem )?> ><?php echo otw_wml_wp_item_attribute( $wp_item_type, 'TITLE', $wpItem ) ?></a>
				</p>
			<?php }?>
		<?php }?>
	<?php }else{ echo '&nbsp;'; }?>
	<input type="hidden" id="otw_total_items_<?php echo esc_attr( $wp_item_type )?>" value="<?php echo esc_attr( $total_valid )?>"/>
	<input type="hidden" id="otw_total_selected_<?php echo esc_attr( $wp_item_type )?>" value="<?php echo esc_attr( $total_selected )?>"/>
	<div class="otw_sidebar_item_pager">
		<input type="hidden" name="otw_type_<?php echo esc_attr( $wp_item_type )?>_page_field" id="otw_type_<?php echo esc_attr( $wp_item_type )?>_page_field" value="<?php echo esc_attr( $current_page );?>" />
		<?php if( isset( $pager_data['links'] ) && count( $pager_data['links']['page'] ) ){?>
			<div class="otw_sidebar_pager_links">
				<?php if( $pager_data['links']['first'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['first'] ) ?>"><?php esc_html_e( 'First', 'otw_wml' )?></a>
				<?php }?>
				<?php if( $pager_data['links']['prev'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['prev'] ) ?>"><?php esc_html_e( 'Previous', 'otw_wml' )?></a>
				<?php }?>
				<?php foreach( $pager_data['links']['page'] as $l_page ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $l_page )?>"<?php if( $l_page == $current_page){ echo 'class="otw_selected_page"';}?>><?php echo $l_page + 1 ?></a>
				<?php }?>
				<?php if( $pager_data['links']['next'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['next'] ) ?>"><?php esc_html_e( 'Next', 'otw_wml' )?></a>
				<?php }?>
				<?php if( $pager_data['links']['last'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['last'] ) ?>"><?php esc_html_e( 'Last', 'otw_wml' )?></a>
				<?php }?>
			</div>
		<?php }?>
		<div class="otw_sidebar_pager_items">
			<label for="otw_type_<?php echo esc_attr( $wp_item_type )?>_per_page_field"><?php esc_html_e( 'Items on Page', 'otw_wml' )?></label>
			<select class="otw_sidebar_items_per_page" id="otw_type_<?php echo esc_attr( $wp_item_type )?>_per_page_field">
				<?php for( $cI = 5; $cI <= 50; $cI++ ){?>
					<?php
						if( $items_limit == $cI ){
							$selected = ' selected="selected"';
						}else{
							$selected = '';
						}
					?>
					<option<?php echo $selected;?>><?php echo esc_html( $cI )?></option>
				<?php }?>
			</select>
		</div>
	</div>
<?php }else{?>
	<div class="f_items">
	<?php if( is_array( $items ) && count( $items ) ){?>
		<?php foreach( $items as $wpItem ) {?>
			<p<?php otw_sidebar_item_attributes( 'p', $wp_item_type, otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ), $otw_sidebar_values, $wpItem )?>>
				<input type="checkbox" id="otw_sbi_<?php echo esc_attr( $wp_item_type )?>_sbi_<?php echo otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ) ?>"<?php otw_sidebar_item_attributes( 'c', $wp_item_type, otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ), $otw_sidebar_values, array() )?> value="<?php echo otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ) ?>" name="otw_sbi_<?php echo esc_attr( $wp_item_type )?>[<?php echo otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ) ?>]" /><label for="otw_sbi_<?php echo esc_attr( $wp_item_type )?>_sbi_<?php echo otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ) ?>"<?php otw_sidebar_item_attributes( 'l', $wp_item_type, otw_wml_wp_item_attribute( $wp_item_type, 'ID', $wpItem ), $otw_sidebar_values, $wpItem )?> ><a href="javascript:;"><?php echo otw_wml_wp_item_attribute( $wp_item_type, 'TITLE', $wpItem ) ?></a></label>
			</p>	
		<?php }?>
	<?php }else{ echo '&nbsp;'; }?>
	</div>
	<div class="otw_sidebar_item_pager">
		<input type="hidden" name="otw_type_<?php echo esc_attr( $wp_item_type )?>_page_field" id="otw_type_<?php echo esc_attr( $wp_item_type )?>_page_field" value="<?php echo esc_attr( $current_page );?>" />
		<?php if( isset( $pager_data['links'] ) && count( $pager_data['links']['page'] ) ){?>
			<div class="otw_sidebar_pager_links">
				<?php if( $pager_data['links']['first'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['first'] ) ?>"><?php esc_html_e( 'First', 'otw_wml' )?></a>
				<?php }?>
				<?php if( $pager_data['links']['prev'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['prev'] ) ?>"><?php esc_html_e( 'Previous', 'otw_wml' )?></a>
				<?php }?>
				<?php foreach( $pager_data['links']['page'] as $l_page ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $l_page )?>"<?php if( $l_page == $current_page){ echo 'class="otw_selected_page"';}?>><?php echo $l_page + 1 ?></a>
				<?php }?>
				<?php if( $pager_data['links']['next'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['next'] ) ?>"><?php esc_html_e( 'Next', 'otw_wml' )?></a>
				<?php }?>
				<?php if( $pager_data['links']['last'] !== false ){?>
					<a href="javascript:;" rel="<?php echo esc_attr( $pager_data['links']['last'] ) ?>"><?php esc_html_e( 'Last', 'otw_wml' )?></a>
				<?php }?>
			</div>
		<?php }?>
		<div class="otw_sidebar_pager_items">
			<label for="otw_type_<?php echo esc_attr( $wp_item_type )?>_per_page_field"><?php esc_html_e( 'Items on Page', 'otw_wml' )?></label>
			<select class="otw_sidebar_items_per_page" id="otw_type_<?php echo esc_attr( $wp_item_type )?>_per_page_field">
				<?php for( $cI = 5; $cI <= 50; $cI++ ){?>
					<?php
						if( $items_limit == $cI ){
							$selected = ' selected="selected"';
						}else{
							$selected = '';
						}
					?>
					<option<?php echo $selected;?>><?php echo esc_html( $cI )?></option>
				<?php }?>
			</select>
		</div>
	</div>
<?php }?>
