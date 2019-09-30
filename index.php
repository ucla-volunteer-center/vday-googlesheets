<?php
/*
Plugin Name: Google Sheets Importer
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Allows connecting Google Sheets to import data into posts or custom post types, supports ACF custom fields. 
Author: Kevin Liu
Version: 0.1
Author URI: http://kevinliu.io
*/

// create custom plugin settings menu
add_action('admin_menu', 'g_sheets_plugin_create_menu');

function g_sheets_plugin_create_menu() {

	//create new top-level menu
	add_menu_page('Google Sheets', 'Google Sheets Import', 'manage_options', 'g_sheets_plugin_settings_page', 'g_sheets_plugin_settings_page');
	
	//call register settings function
	add_action( 'admin_init', 'register_g_sheets_plugin_settings' );
}


function register_g_sheets_plugin_settings() {
	//register our settings
	register_setting( 'g-sheets-plugin-settings-group', 'sheets_url' );
	register_setting( 'g-sheets-plugin-settings-group', 'cpt_slug' );
	register_setting( 'g-sheets-plugin-settings-group', 'g_title' );
	register_setting( 'g-sheets-plugin-settings-group', 'g_content' );
	register_setting( 'g-sheets-plugin-settings-group', 'g_acf_group' );
	register_setting( 'g-sheets-plugin-settings-group', 'g_custom_fields' );
	register_setting( 'g-sheets-plugin-settings-group', 'g_delete_posts' );
	register_setting( 'g-sheets-plugin-settings-group', 'g_custom_slugs' );
	register_setting( 'g-sheets-plugin-settings-group', 'cpt_status' );

}

/**
 * Main Import Button & Options
 **/

function g_sheets_plugin_settings_page() {
$spreadsheet_url = get_option('sheets_url');
?>

<div class="wrap">

<h1>Google Sheets Importer</h1>

<hr>

<h1>Sheet Configuration</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'g-sheets-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'g-sheets-plugin-settings-group' ); ?>
    <table class="form-table">
   
        <tr valign="top">
        <th scope="row">Google Sheets URL</th>
        <td><input style="width:100%;" type="text" name="sheets_url" value="<?php echo esc_attr( get_option('sheets_url') ); ?>" /></td>
        </tr>
        
        <?php if($spreadsheet_url):?>

        <tr valign="top">
        <th scope="row">Post Type</th>
        <td><input type="text" name="cpt_slug" placeholder="eg post, products, portfolio" value="<?php echo esc_attr( get_option('cpt_slug') ); ?>" /></td>
        </tr>

        <!-- <tr valign="top">
        <th scope="row">Custom Taxonomy</th>
        <td><input type="text" name="cpt_taxonomy" value="<//php echo esc_attr( get_option('cpt_taxonomy') ); ?>" /></td>
        </tr> -->

        <tr valign="top">
        <th scope="row">Posts Status</th>
        <i>Posts Status: publish, draft or pending</i>
        <td><input type="text" name="cpt_status" placeholder="eg publish, draft or pending" value="<?php echo esc_attr( get_option('cpt_status') ); ?>" /></td>
        </tr>

   		<?php endif;?>
   
	</table>

	<hr>

	<?php if($spreadsheet_url):?>

	<h1>Mandatory Field Mapping</h1>
    <i>Add the actual column name from your google sheet as it appears (case sensitive)</i>
	 
	 <table class="form-table">
	 		
	 	<tr valign="top">
        <th scope="row">Post Title</th>
        <td><input type="text" name="g_title" value="<?php echo esc_attr( get_option('g_title') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Post Content</th>
        <td><input type="text" name="g_content" value="<?php echo esc_attr( get_option('g_content') ); ?>" /></td>
        </tr>

	 </table>

	 <hr>

	 <h1>Other Mapping</h1>
	 <i>Map the columns from your Google Spreadsheet</i>
	 </br>

	 <table class="form-table">
	 		
	 	<tr valign="top">
        <th scope="row">Map Google Spreadsheet Columns</th>
        <td><input style="width:100%;" type="text" name="g_custom_fields" value="<?php echo esc_attr( get_option('g_custom_fields') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">To these Custom Fields (enter field key)</th>
        <td><input style="width:100%;" type="text" name="g_custom_slugs" value="<?php echo esc_attr( get_option('g_custom_slugs') ); ?>" /></td>
        </tr>

	 </table>
	 
	 <hr>

 	<?php endif;?>
    
    <?php submit_button(); ?>

</form>

 <hr>

<?php if($spreadsheet_url):?>

<?php
include_once plugin_dir_path( __FILE__ ).'./processing.php';
?>

<?php else:?>

<h3>Enter a Google Sheets share URL and 'SAVE' to get configuration options<h3>

<?php endif;?>

</div>

<?php } ?>