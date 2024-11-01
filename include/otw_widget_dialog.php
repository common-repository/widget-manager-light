<?php
/** OTW widget appearence dialog
  *
  */
$sidebar = '';
$widget = '';

if( otw_get( 'sidebar', false ) ){
	$sidebar = otw_get( 'sidebar', '' );
}
if( otw_get( 'widget', false ) ){
	$widget = otw_get( 'widget', '' );
}


global $wp_registered_sidebars, $wp_wml_int_items, $otw_wml_plugin_url;

$with_exclude_items = array( 'postsincategory', 'postsintag', 'post_in_ctx' );

if( !$sidebar && $widget ){
	//try to find sibar by widget
	
	$sidebar_widgets = get_option('sidebars_widgets');
	$found_sizebars = array();
	
	if( is_array( $sidebar_widgets ) ){
		foreach( $sidebar_widgets  as $s_sidebar => $s_widgets ){
			foreach( $s_widgets as $s_widget ){
				
				if( $s_widget == $widget ){
					$found_sizebars[ $s_sidebar ] = $s_sidebar;
				}
			}
		}
	}
	
	if( count( $found_sizebars ) == 1 ){
		
		foreach( $found_sizebars as $s_bar ){
			$sidebar = $s_bar;
		}
	}
}

//validate input data
if( !$sidebar || !$widget ){
	wp_die( esc_html__( 'Invalid sidebar or widget' ) );
}



//validate that this sidebar exists
if( !isset( $wp_registered_sidebars[ $sidebar ] ) ){
	wp_die( esc_html__( 'Requested not registered sidebar' ) );
}

$otw_sidebars = get_option( 'otw_sidebars' );

if( !is_array( $otw_sidebars ) ){
	$otw_sidebars = array();
}

$sidebar_widgets = get_option('sidebars_widgets');

//check if widget is part of this sidebar
if( !isset( $sidebar_widgets[ $sidebar ] ) || !count( $sidebar_widgets[ $sidebar ] ) || !in_array( $widget, $sidebar_widgets[ $sidebar ]  ) ){
	wp_die( esc_html__( 'Requested widget is not assinged to this sidebar' ) );
}

if( otw_post('otw_action',false) && in_array( otw_post( 'otw_action', '' ), array( 'exclude_posts' ) ) ){

	$response = 0;
	
	$otw_widget_settings = get_option( 'otw_widget_settings' );
	
	if( !isset( $otw_widget_settings[ $sidebar ] ) ){
		$otw_widget_settings[ $sidebar ] = array();
	}
	
	$value = '';
	
	if( otw_post( 'posts', false ) ){
		$value = trim( otw_post( 'posts', '' ) );
	}
	
	if( otw_post( 'item_type', false ) && strlen( otw_post( 'item_type', '' ) ) ){
	
		$item_type = otw_post( 'item_type', '' );
		
		if( !isset( $otw_widget_settings[ $sidebar ][ $item_type ] ) ){
			$otw_widget_settings[ $sidebar ][ $item_type ] = array();
		}
		
		if( !isset( $otw_widget_settings[ $sidebar ][ $item_type ]['_otw_ep'] ) || !is_array( $otw_widget_settings[ $sidebar ][ $item_type ]['_otw_ep'] ) ){
			$otw_widget_settings[ $sidebar ][ $item_type ]['_otw_ep'] = array();
		}
		$otw_widget_settings[ $sidebar ][ $item_type ]['_otw_ep'][ $widget ] = $value;
		
		$response = 1;
		
	}
	update_option( 'otw_widget_settings', $otw_widget_settings );
	
	echo $response;
	
	return;
	
}
if( otw_post('otw_action',false) && in_array( otw_post( 'otw_action', '' ), array( 'vis', 'invis' ) ) ){

	if( otw_post( 'item_type', false ) ){
		
		$response = '';
		$otw_widget_settings = get_option( 'otw_widget_settings' );
		
		if( !isset( $otw_widget_settings[ $sidebar ] ) ){
			$otw_widget_settings[ $sidebar ] = array();
		}
		
		if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ] ) ){
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ] = array();
		}
		
		$current_wc = '';
		if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'] ) || !is_array( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'] ) ){
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'] = array();
		}
		
		if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] ) ){
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] = '';
		}else{
			$current_wc = $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ];
		}
		
		if( $current_wc == otw_post( 'otw_action', '' ) ){
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] = '';
			$response = 'none';
		}else{
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] = otw_post( 'otw_action', '' );
			$response  = otw_post( 'otw_action', '' );
		}
		
		update_option( 'otw_widget_settings', $otw_widget_settings );
		
		echo $response;
		
		return;
	}
	
}
if( otw_post('otw_action',false) && ( otw_post( 'otw_action', '' ) == 'update' ) ){
	
	if( otw_post( 'item_type', false ) && otw_post( 'item_id', false ) ){
	
		$otw_widget_settings = get_option( 'otw_widget_settings' );
		
		if( !isset( $otw_widget_settings[ $sidebar ] ) ){
			$otw_widget_settings[ $sidebar ] = array();
		}
		
		//create item selection if not create but all used
		if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ] ) ){
			
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ] = array();
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['id'] = otw_post( 'item_id', '' );
			$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'] = array();
			
		}
		
		//process action to excluded widgets
		if( isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][$widget] ) && in_array( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ],array( 'vis', 'invis' ) ) ){
		
			if( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] == 'invis' ){
				
				
				if( is_array( $otw_sidebars ) && array_key_exists( $sidebar, $otw_sidebars ) ){
					
					if( isset( $wp_registered_sidebars[ $sidebar ]['validfor'][ otw_post( 'item_type', '' ) ] ) ){
						
						foreach( $wp_registered_sidebars[ $sidebar ]['validfor'][ otw_post( 'item_type', '' ) ] as $wp_sb_item_id => $wp_sb_item_data ){
							
							if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ] ) ){
								
								$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ] = array();
								$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['id'] = $wp_sb_item_id;
								$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'] = array();
							}
							$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'][ $widget ] = $widget;
						}
					}
				}else{
					$wp_all_items = otw_get_wp_items( otw_post( 'item_type', '' ) );
					
					if( is_array( $wp_all_items ) && count( $wp_all_items ) ){
						
						foreach( $wp_all_items as $wp_all_item ){
							
							$wp_sb_item_id = otw_wml_wp_item_attribute( otw_post( 'item_type', '' ), 'ID', $wp_all_item );
							if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ] ) ){
								
								$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ] = array();
								$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['id'] = $wp_sb_item_id;
								$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'] = array();
							}
							$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'][ $widget ] = $widget;
						}
					}
				}
				
				if( isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ] ) ){
					unset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ] );
					echo 'sitem_selected_from_invis';
				}else{
					echo 'sitem_selected_from_invis';
				}
				unset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] );
				
			}elseif( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] == 'vis' ){
				
				
				if( is_array( $otw_sidebars ) && array_key_exists( $sidebar, $otw_sidebars ) ){
					
					if( isset( $wp_registered_sidebars[ $sidebar ]['validfor'][ otw_post( 'item_type', '' ) ] ) ){
						
						foreach( $wp_registered_sidebars[ $sidebar ]['validfor'][ otw_post( 'item_type', '' ) ] as $wp_sb_item_id => $wp_sb_item_data ){
							
							if( isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'][ $widget ] ) ){
								unset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'][ $widget ] );
							}
						}
					}
				}else{
					$wp_all_items = otw_get_wp_items( otw_post( 'item_type', '' ) );
					
					if( is_array( $wp_all_items ) && count( $wp_all_items ) ){
						
						foreach( $wp_all_items as $wp_all_item ){
							
							$wp_sb_item_id = otw_wml_wp_item_attribute( otw_post( 'item_type', '' ), 'ID', $wp_all_item );
							if( isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'][ $widget ] ) ){
								unset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ $wp_sb_item_id ]['exclude_widgets'][ $widget ] );
							}
						}
					}
				}
				
				if( !isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ] ) ){
					$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ] = $widget;
					echo 'sitem_selected_from_vis';
				}else{
					echo 'sitem_selected_from_vis';
				}
				
				unset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ]['_otw_wc'][ $widget ] );
				
			}
			
		}elseif( isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ] ) ){
			if( isset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ] ) ){
				unset( $otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ] );
				echo 'sitem_selected';
			}else{
				$otw_widget_settings[ $sidebar ][ otw_post( 'item_type', '' ) ][ otw_post( 'item_id', '' ) ]['exclude_widgets'][ $widget ]  = $widget;
				echo 'sitem_notselected';
			}
			
		}
		
		update_option( 'otw_widget_settings', $otw_widget_settings );
	}
	return;
}
$otw_widget_settings = get_option( 'otw_widget_settings' );

/** set class name for all selection links
 *
 *  @param string $type vis|invis
 *  @param string $sidebar
 *  @param string $widget
 *  @param string $wp_item_type
 *  @return string
 */
function otw_sidebar_item_all_class( $type, $sidebar, $widget, $wp_item_type ){

	global $wp_registered_sidebars;
	$class = '';
	
	if( isset( $wp_registered_sidebars[ $sidebar ]['widgets_settings'][ $wp_item_type ]['_otw_wc'][ $widget ] ) ){
	
		if( $wp_registered_sidebars[ $sidebar ]['widgets_settings'][ $wp_item_type ]['_otw_wc'][ $widget ] == $type ){
			$class .= ' all_selected';
		}
	}
	
	echo $class;
}


foreach( $wp_wml_int_items as $wp_item_type => $wp_item_data ){
	
	if( is_array( $otw_sidebars ) && array_key_exists( $sidebar, $otw_sidebars ) ){
	
		if( isset( $wp_registered_sidebars[ $sidebar ]['validfor'][ $wp_item_type ] )  && count( $wp_registered_sidebars[ $sidebar ]['validfor'][ $wp_item_type ] )){
			$wp_wml_int_items[ $wp_item_type ][0] = array( 1 );
		}else{
			$wp_wml_int_items[ $wp_item_type ][0] = array();
		}
	}else{
		$wp_wml_int_items[ $wp_item_type ][0] = array( 1 );
	}
}

?>
<div class="otw_dialog_content" id="otw_dialog_content">

<div class="d_info">
	<div class="updated visupdated">
		<p><?php esc_html_e( 'A selected page template includes all pages using that template.', 'otw_wml' )?><br />
		<?php esc_html_e( 'Template hierarchy Page includes all pages.', 'otw_wml' )?></p>
	</div>
</div>
<?php if( is_array( $wp_wml_int_items ) && count( $wp_wml_int_items ) ){?>
	
	<?php foreach( $wp_wml_int_items as $wp_item_type => $wp_item_data ){?>
		
		<?php if( is_array( $wp_item_data[0] ) && count( $wp_item_data[0] ) ){?>
			<div class="meta-box-sortables metabox-holder">
				<div class="postbox">
					<div title="<?php esc_html_e('Click to toggle', 'otw_wml')?>" class="handlediv sitem_toggle"><br></div>
					<h3 class="hndle sitem_header" title="<?php esc_html_e('Click to toggle', 'otw_wml')?>"><span><?php echo esc_html( $wp_item_data[1] )?></span></h3>
					
					<div class="inside sitems<?php if( count( $wp_item_data[0] ) > 15 ){ echo ' mto';}?>" id="otw_sbm_app_type_<?php echo esc_attr( $wp_item_type )?>" rel="<?php echo $sidebar?>|<?php echo $widget?>|<?php echo $wp_item_type?>" >
						<div class="otw_sidebar_wv_item_filter">
							<div id="otw_type_page_wv_search" class="otw_sidebar_wv_filter_search">
								<label for="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_search_field"><?php esc_html_e( 'Search', 'otw_wml' )?></label>
								<input type="text" id="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_search_field" class="otw_sbm_wv_q_filter" value=""/>
							</div>
							<div id="otw_type_page_wv_clear" class="otw_sidebar_wv_filter_clear">
								<a href="javascript:;" id="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_wv_clear"><?php esc_html_e( 'reset', 'otw_wml' )?></a>
							</div>
							<div id="otw_type_page_wv_order" class="otw_sidebar_wv_filter_order">
								<label for="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_order_field"><?php esc_html_e( 'Order', 'otw_wml' )?></label>
								<select id="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_order_field">
									<?php $sort_options = otw_wml_get_item_sort_options( $wp_item_type);?>
									<?php if( count( $sort_options ) ){?>
										<?php foreach( $sort_options as $s_key => $s_value ){ ?>
											<option value="<?php echo esc_attr( $s_key )?>"><?php echo esc_html( $s_value )?></option>
										<?php }?>
									<?php }?>
								</select>
							</div>
							<div id="otw_type_page_wv_show" class="otw_sidebar_wv_filter_show">
								<label for="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_show_field"><?php esc_html_e( 'Show', 'otw_wml' )?></label>
								<select id="otw_type_<?php echo esc_attr( $wp_item_type ) ?>_show_field">
									<option value="all"><?php esc_html_e( 'All', 'otw_wml' )?></option>
									<option value="all_selected"><?php esc_html_e( 'All Selected', 'otw_wml' )?></option>
									<option value="all_unselected"><?php esc_html_e( 'All Unselected', 'otw_wml' )?></option>
								</select>
							</div>
							
						</div>
						<div class="otw_sbm_all_actions">
							<div class="otw_sbm_all_links">
								<a href="javascript:;" class="otw_sbm_select_all_items all_vis" rel="<?php echo $sidebar?>|<?php echo $widget?>|<?php echo $wp_item_type?>|vis"><?php esc_html_e( 'Select All', 'otw_wml' )?></a>
									|
								<a href="javascript:;" class="otw_sbm_unselect_all_items all_invis" rel="<?php echo $sidebar?>|<?php echo $widget?>|<?php echo $wp_item_type?>|invis"><?php esc_html_e( 'Unselect All', 'otw_wml' )?></a>
							</div>
							<div class="otw_sbm_selected_items">
								<span class="otw_selected_items_number"></span>&nbsp;<span class="otw_seleted_items_plural"><?php esc_html_e( 'items are', 'otw_wml' );?></span><span class="otw_selected_items_singular"><?php esc_html_e('item is', 'otw_wml' )?></span>&nbsp;<?php esc_html_e( 'selected', 'otw_wml' )?>
							</div>
						</div>
						<div class="lf_items">
						</div>
						<?php
							$exclude_type = $wp_item_type;
							
							if( preg_match( "/^post_in_ctx_(.*)$/", $exclude_type ) ){
								$exclude_type = 'post_in_ctx';
							}
							
							if( in_array( $exclude_type, $with_exclude_items ) ){?>
								<div class="otw_widget_exclude_items">
									<span><?php esc_html_e( 'Exclude posts from the above result-set by given id or slug. Separate with commas.' ); ?></span><br />
									<input type="text" id="otw_exclude_posts_<?php echo esc_attr( $wp_item_type ) ?>"  name="otw_exclude_posts_<?php echo esc_attr( $wp_item_type ) ?>" value="<?php echo otw_wp_item_widget_exclude( 'post', $sidebar, $widget, $wp_item_type, $otw_widget_settings ) ?>" />
									<input type="button" id="otw_save_excluded_<?php echo esc_attr( $wp_item_type ) ?>" value="<?php esc_html_e( 'Save', 'otw_wml' ) ?>" class="button otw_save_excluded" rel="<?php echo $sidebar?>|<?php echo $widget?>|<?php echo $wp_item_type?>" />
									<img src="<?php echo $otw_wml_plugin_url ?>images/loading.gif" border="0" id="otw_exclude_loading_<?php echo esc_attr( $wp_item_type ) ?>" />
								</div>
						<?php }?>

					</div>
					
				</div>
			</div>

		<?php }?>
	<?php }?>
	<script type="text/javascript">
		otw_init_appearence_dialog();
	</script>
<?php }?>
</div>
