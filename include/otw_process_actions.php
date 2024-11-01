<?php
/**
 * Process otw actions
 *
 */

if( otw_post( 'otw_wml_action', false ) ){
	
	require_once( ABSPATH . WPINC . '/pluggable.php' );
	
	switch( otw_post( 'otw_wml_action', '' ) ){
		
		case 'activate_otw_sidebar':
				if( otw_post( 'cancel', false ) ){
					wp_redirect( 'admin.php?page=otw-sbm' );
				}else{
					$otw_sidebars = get_option( 'otw_sidebars' );
					
					if( otw_get( 'sidebar', false ) && isset( $otw_sidebars[ otw_get( 'sidebar', '' ) ] ) ){
						$otw_sidebar_id = otw_get( 'sidebar', '' );
						
						$otw_sidebars[ $otw_sidebar_id ]['status'] = 'active';
						
						update_option( 'otw_sidebars', $otw_sidebars );
						
						wp_redirect( 'admin.php?page=otw-sbm&message=3' );
					}else{
						wp_die( esc_html__( 'Invalid sidebar' ) );
					}
				}
			break;
		case 'deactivate_otw_sidebar':
				if( otw_post( 'cancel', false ) ){
					wp_redirect( 'admin.php?page=otw-sbm' );
				}else{
					$otw_sidebars = get_option( 'otw_sidebars' );
					
					if( otw_get( 'sidebar', false ) && isset( $otw_sidebars[ otw_get( 'sidebar', '' ) ] ) ){
						$otw_sidebar_id = otw_get( 'sidebar', '' );
						
						$otw_sidebars[ $otw_sidebar_id ]['status'] = 'inactive';
						
						update_option( 'otw_sidebars', $otw_sidebars );
						
						wp_redirect( 'admin.php?page=otw-sbm&message=4' );
					}else{
						wp_die( esc_html__( 'Invalid sidebar' ) );
					}
				}
			break;
		case 'delete_otw_sidebar':
				if( otw_post( 'cancel', false ) ){
					wp_redirect( 'admin.php?page=otw-sbm' );
				}else{
					
					$otw_sidebars = get_option( 'otw_sidebars' );
					
					if( otw_get( 'sidebar', false ) && isset( $otw_sidebars[ otw_get( 'sidebar', '' ) ] ) ){
						$otw_sidebar_id = otw_get( 'sidebar', '' );
						
						$new_sidebars = array();
						
						//remove the sidebar from otw_sidebars
						foreach( $otw_sidebars as $sidebar_key => $sidebar ){
						
							if( $sidebar_key != $otw_sidebar_id ){
							
								$new_sidebars[ $sidebar_key ] = $sidebar;
							}
						}
						update_option( 'otw_sidebars', $new_sidebars );
						
						//remove sidebar from widget
						$widgets = get_option( 'sidebars_widgets' );
						
						if( isset( $widgets[ $otw_sidebar_id ] ) ){
							
							$new_widgets = array();
							foreach( $widgets as $sidebar_key => $widget ){
								if( $sidebar_key != $otw_sidebar_id ){
								
									$new_widgets[ $sidebar_key ] = $widget;
								}
							}
							update_option( 'sidebars_widgets', $new_widgets );
						}
						
						wp_redirect( admin_url( 'admin.php?page=otw-sbm&message=2' ) );
					}else{
						wp_die( esc_html__( 'Invalid sidebar' ) );
					}
				}
			break;
		case 'manage_otw_options':
				
				$options = array();
				
				if( otw_post( 'sbm_activate_appearence', false ) && strlen( trim( otw_post( 'sbm_activate_appearence', '' ) ) ) ){
					$options['activate_appearence'] = true;
				}else{
					$options['activate_appearence'] = false;
				}
				update_option( 'otw_plugin_options', $options );
				
				if( otw_post( 'otw_sbm_promotions', false ) && !empty( otw_post( 'otw_sbm_promotions', '' ) ) ){
					
					global $otw_wml_factory_object, $otw_wml_plugin_id;
					
					update_option( $otw_wml_plugin_id.'_dnms', otw_post( 'otw_sbm_promotions', '' ) );
					
					if( is_object( $otw_wml_factory_object ) ){
						$otw_wml_factory_object->retrive_plungins_data( true );
					}
				}

				wp_redirect( admin_url( 'admin.php?page=otw-wml&message=1' ) );
			break;
		case 'manage_otw_sidebar':
				global $validate_messages, $wp_wml_int_items;
				$validate_messages = array();
				$valid_page = true;
				if( !otw_post( 'sbm_title', false ) || !strlen( trim( otw_post( 'sbm_title', '' ) ) ) ){
					$valid_page = false;
					$validate_messages[] = esc_html__( 'Please type valid sidebar title' );
				}
				if( !otw_post( 'sbm_status', false ) || !strlen( trim( otw_post( 'sbm_status', '' ) ) ) ){
					$valid_page = false;
					$validate_messages[] = esc_html__( 'Please select status' );
				}
				if( $valid_page ){
					
					$otw_sidebars = get_option( 'otw_sidebars' );
					
					if( !is_array( $otw_sidebars ) ){
						$otw_sidebars = array();
					}
					$items_to_remove = array();
					if( otw_get( 'sidebar', false ) && isset( $otw_sidebars[ otw_get( 'sidebar', '' ) ] ) ){
						$otw_sidebar_id = otw_get( 'sidebar', '' );
						$sidebar = $otw_sidebars[ otw_get( 'sidebar', '' ) ];
						$items_to_remove = $sidebar['validfor'];
					}else{
						$sidebar = array();
						$otw_sidebar_id = false;
					}
					
					$sidebar['title'] = (string) otw_post( 'sbm_title', '' );
					$sidebar['description'] = (string) otw_post( 'sbm_description', '' );
					$sidebar['replace'] = (string) otw_post( 'sbm_replace', '' );
					$sidebar['status'] = (string) otw_post( 'sbm_status', '' );
					$sidebar['widget_alignment'] = (string) otw_post( 'sbm_widget_alignment', '' );
					
					//save selected items
					$otw_sbi_items = array_keys( $wp_wml_int_items );
					
					foreach( $otw_sbi_items as $otw_sbi_item ){
						
						if( otw_post( 'otw_sbi_'.$otw_sbi_item, false ) && is_array( otw_post( 'otw_sbi_'.$otw_sbi_item, '' ) ) ){
							
							if( !isset( $sidebar['validfor'][ $otw_sbi_item ] ) ){
							
								$sidebar['validfor'][ $otw_sbi_item ] = array();
							}
							
							foreach( otw_post( 'otw_sbi_'.$otw_sbi_item, '' ) as $item_id ){
								
								if( !isset( $sidebar['validfor'][ $otw_sbi_item ][ $item_id ] ) ){
									$sidebar['validfor'][ $otw_sbi_item ][ $item_id ] = array();
									$sidebar['validfor'][ $otw_sbi_item ][ $item_id ]['id'] = $item_id;
								}else{
									unset( $items_to_remove[ $otw_sbi_item ][ $item_id ] );
								}
								
							}
							
						}else{
							$sidebar['validfor'][ $otw_sbi_item ] = array();
						}
					}
					
					//remove any not selected items
					if( is_array( $items_to_remove ) && count( $items_to_remove ) ){
						
						foreach( $items_to_remove as $item_type => $item_data ){
							
							foreach( $item_data as $item_id => $item_info ){
								if( isset( $sidebar['validfor'][ $item_type ][ $item_id ] ) ){
									unset( $sidebar['validfor'][ $item_type ][ $item_id ] );
								}
							}
						}
					}
					
					if( $otw_sidebar_id === false ){
						
						$otw_sidebar_id = 'otw-sidebar-'.( get_next_otw_sidebar_id() );
						$sidebar['id'] = $otw_sidebar_id;
					}
					$otw_sidebars[ $otw_sidebar_id ] = $sidebar;
					
					update_option( 'otw_sidebars', $otw_sidebars );
					
					wp_redirect( 'admin.php?page=otw-sbm&message=1' );
				}
			break;
	}
}

?>