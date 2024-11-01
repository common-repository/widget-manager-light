<?php
/** Manage plugin options
  *
  */

$otw_options = get_option( 'otw_plugin_options' );

global $otw_wml_plugin_id;


$db_values = array();
$db_values['otw_smb_promotions'] = get_option( $otw_wml_plugin_id.'_dnms' );

if( empty( $db_values['otw_smb_promotions'] ) ){
	$db_values['otw_smb_promotions'] = 'on';
}


$message = '';
$massages = array();
$messages[1] = 'Options saved.';


if( otw_get('message',false) && isset( $messages[ otw_get('message','') ] ) ){
	$message .= $messages[ otw_get('message','') ];
}
?>


<?php if ( $message ) : ?>
<div id="message" class="updated"><p><?php echo esc_html( $message ); ?></p></div>
<?php endif; ?>
<div class="wrap">
	<div id="icon-edit" class="icon32"><br/></div>
	<h2>
		<?php esc_html_e('Plugin Options') ?>
	</h2>
	<div class="form-wrap" id="poststuff">
		<form method="post" action="" class="validate">
			<input type="hidden" name="otw_wml_action" value="manage_otw_options" />
			<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('otw-sbm-options'); ?>

			<div id="post-body">
				<div id="post-body-content">
					<div class="form-field">
						<label for="otw_sbm_promotions"><?php esc_html_e('Show OTW Promotion Messages in my WordPress admin', 'otw_wpl'); ?></label>
						<select id="otw_sbm_promotions" name="otw_sbm_promotions">
							<option value="on" <?php echo ( isset( $db_values['otw_smb_promotions'] ) && ( $db_values['otw_smb_promotions'] == 'on' ) )? 'selected="selected"':''?>>on(default)</option>
							<option value="off"<?php echo ( isset( $db_values['otw_smb_promotions'] ) && ( $db_values['otw_smb_promotions'] == 'off' ) )? 'selected="selected"':''?>>off</option>
						</select>
					</div>
					<div class="form-field">
						
						<label for="sbm_activate_appearence" class="selectit"><?php esc_html_e( 'Enable widgets management' )?>
						<input type="checkbox" id="sbm_activate_appearence" name="sbm_activate_appearence" value="1" style="width: 15px;" <?php if( isset( $otw_options['activate_appearence'] ) && $otw_options['activate_appearence'] ){ echo ' checked="checked" ';}?> /></label>
						<p><?php esc_html_e( 'Control every single widgets visibility on different pages. When widget control is enabled it will add a button called Set Visibility at the bottom of each widgets panel (Appearance -> Widgets).  You can choose where is the widget displayed on or hidden from.' );?></p>
					</div>
					<p class="submit">
						<input type="submit" value="<?php esc_html_e( 'Save Options') ?>" name="submit" class="button"/>
					</p>
				</div>
			</div>
		</form>
	</div>
</div>
