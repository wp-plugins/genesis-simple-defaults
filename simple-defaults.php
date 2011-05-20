<?php
/*************************************************************************
 * Plugin Name: Simple Defaults 
 * Author: Hit Reach
 * Author URI: http://www.hitreach.co.uk/
 * Description: Allows the settings of some defaults for Genesis themes
 * Version: 0.0.1
 * Plugin URI: http://www.hireach.co.uk/wordpress-plugins/simple-defaults/
 * License: GPL2
*************************************************************************/
#Copyright
/*************************************************************************
	Copyright 2010  Hit Reach  (email : jamie.fraser@hitreach.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*************************************************************************/
#Go!
/**********************
	Activation Hook
**********************/
register_activation_hook(__FILE__, 'SD_HR_Activate');
function SD_HR_Activate(){
	$latest = '1.5';
	$theme_info = get_theme_data(TEMPLATEPATH.'/style.css');
	if(basename(TEMPLATEPATH) != 'genesis') {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die('Sorry, you can\'t activate unless you have installed <a href="http://www.studiopress.com/themes/genesis">Genesis</a>');
	}
	if(version_compare( $theme_info['Version'], $latest, '<') ) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die('Sorry, you can\'t activate without <a href="http://www.studiopress.com/support/showthread.php?t=19576">Genesis '.$latest.'</a> or greater');
	}
	if(!get_option( "SD_HR_OPTIONS" )){
		add_option("SD_HR_OPTIONS");
		$options = array('comment-title' => 'Speak Your Mind',
						 'thumb_70' => "",
						 'thumb_110' => "",
						 'thumb_150' => "",
						 'favicon' =>"");						 
		update_option("SD_HR_OPTIONS", $options);
		add_theme_support('genesis-simple-defaults');
	}}
/**********************
	Comment Title Filter
**********************/
add_filter('genesis_comment_form_args', 'SD_HR_CT_FILTER');
function SD_HR_CT_FILTER($args){
	if(get_option("SD_HR_OPTIONS")){
		if(is_serialized(get_option("SD_HR_OPTIONS"))){
			$option = unserialize(get_option("SD_HR_OPTIONS"));
		}
		else{
			$option = get_option("SD_HR_OPTIONS");
		}						
		$title = $option['comment-title'];
		$args['title_reply'] = $title;
 	  	return $args;
	}
	else{
		return $args;
	}}
/**********************
	Image Fallback Filter
**********************/
add_filter('genesis_get_image', 'SD_HR_DIF_FILTER', 10, 2);
function SD_HR_DIF_FILTER($output, $args) {
    global $post;
	if(get_option("SD_HR_OPTIONS")){
		if(is_serialized(get_option("SD_HR_OPTIONS"))){
			$option = unserialize(get_option("SD_HR_OPTIONS"));
		}
		else{
			$option = get_option("SD_HR_OPTIONS");
		}
		if( $output || $args['size'] == 'full' || $args['size'] == 'medium' )
			return $output;
		switch($args['size']) {
			case 'Mini Square' :
				// Create file. Must be 70x70 pixels
				$thumbnail = $option['thumb_70'];
				break;
			case 'Square' :
				// Create file. Must be 110x110 pixels
				$thumbnail = $option['thumb_110'];
				break;
			default :
				// Create file. Must be 150x150 pixels
				$thumbnail = $option['thumb_150'];
				break;
		}
		switch($args['format']) {
			case 'html' :
				return '<img src="'.$thumbnail.'" alt="'. get_the_title($post->ID) .'" />';
				break;
			case 'url' :
				return $thumbnail;
				break;
			default :
				return $output;
				break;
		}
	}}
/**********************
	Option Menu
**********************/	
add_action('admin_menu', 'SD_HR_MENU_ACTION');
function SD_HR_MENU_ACTION(){
	global	$_genesis_theme_settings_pagehook;
	$user = wp_get_current_user();
	add_menu_page( "Simple Defaults", "Simple Defaults", "manage_options", "simple-defaults", "SD_HR_MENU");
}

/**********************
	Options Page
**********************/
function SD_HR_MENU(){
	if(get_option("SD_HR_OPTIONS")){
		if(is_serialized(get_option("SD_HR_OPTIONS"))){
			$option = unserialize(get_option("SD_HR_OPTIONS")); }
		else{$option = get_option("SD_HR_OPTIONS");}						
		$title = $option['comment-title'];
		$thumb_70 = $option['thumb_70'];
		$thumb_110 = $option['thumb_110'];
		$thumb_150 = $option['thumb_150'];
		$favicon = $option['favicon'];
	}
	else{
		$options = array('comment-title' => 'Speak Your Mind',
						 'thumb_70' => "",
						 'thumb_110' => "",
						 'thumb_150' => "",
						 'favicon' =>"");
		update_option("SD_HR_OPTIONS", $options);
		add_option("SD_HR_OPTIONS");
		$title = $option['comment-title'];
		$thumb_70 = $option['thumb_70'];
		$thumb_110 = $option['thumb_110'];
		$thumb_150 = $option['thumb_150'];
		$favicon = $option['favicon'];
	}
?>
	<form method="post" action="options.php"> 
	<?php wp_nonce_field('update-options'); ?>
	<table class="form-table" width="90%">
		<tr valign="top">
			<th scope="row"><label for="SD_HR_OPTIONS['comment-title']">Comment Reply Title: </label></th>
			<td><input type="text" name="SD_HR_OPTIONS['comment-title']" id="SD_HR_OPTIONS['comment-title']"  style='width:88%; cursor:pointer;' value="<?php echo $title ?>" /></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="SD_HR_OPTIONS['thumb_70']">Default Thumbnail Image Url <span style='color:red'>70px x 70px</span>: </label></th>
			<td><input type="text" name="SD_HR_OPTIONS['thumb_70']" id="SD_HR_OPTIONS['thumb_70']"  style='width:88%; cursor:pointer;' value="<?php echo $thumb_70 ?>" /></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="SD_HR_OPTIONS['thumb_110']">Default Thumbnail Image Url <span style='color:red'>110px x 110px</span>: </label></th>
			<td><input type="text" name="SD_HR_OPTIONS['thumb_110']" id="SD_HR_OPTIONS['thumb_110']"  style='width:88%; cursor:pointer;' value="<?php echo $thumb_110 ?>" /></td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="SD_HR_OPTIONS['thumb_150']">Default Thumbnail Image Url <span style='color:red'>150px x 150px</span>: </label></th>
			<td><input type="text" name="SD_HR_OPTIONS['thumb_150']" id="SD_HR_OPTIONS['thumb_150']"  style='width:88%; cursor:pointer;' value="<?php echo $thumb_70 ?>" /></td>
		</tr>
	</table>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="SD_HR_OPTIONS" />
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	
	<h2>Upload Favicon</h2>
	<p>Uploading a new Favicon will overwrite previously uploaded icons</p>
	<?php if($favicon != ""){echo "Current Favicon: <img src='$favicon' alt='FavIcon' />";}?>
	<form method="post" enctype='multipart/form-data' action="<?php echo plugins_url('uploadFavicon.php', __FILE__)?>"> 
	<?php wp_nonce_field(plugin_basename(__FILE__),'upload-favicon'); ?>
	<label for='iconImage'>Choose Your Image (.ico | .jpg | .gif | .png) <em>for best results use .ico</em></label><br/>
	<input type='file' name='iconImage' id='iconImage' accesskey="i" /><br/><input type='submit' value='upload' class="button-primary" />
	</form>
<?php  }
/**********************
	Load Favicon
**********************/
add_action('wp_head', 'SD_HR_LOAD_FAVICON');
add_filter('genesis_pre_load_favicon', 'SD_HR_CUST_FAV');
function SD_HR_CUST_FAV($favicon_url){
	if(is_serialized(get_option("SD_HR_OPTIONS"))){
		$option = unserialize(get_option("SD_HR_OPTIONS")); 
	}
	else{$option = get_option("SD_HR_OPTIONS");}						
	$favicon = $option['favicon'];
	if($favicon != ""){
		$favicon_url = $favicon;
	}
	return $favicon_url;
}
function SD_HR_LOAD_FAVICON(){
	if(get_option("SD_HR_OPTIONS")){
		if(is_serialized(get_option("SD_HR_OPTIONS"))){
			$option = unserialize(get_option("SD_HR_OPTIONS")); }
		else{$option = get_option("SD_HR_OPTIONS");}						
		$favicon = $option['favicon'];
			echo '<link rel="shortcut icon" href="'.$favicon.'" />';
			echo '<meta name="custFav" content="'.$favicon.'"/>';
	}
	else{ return; }}
?>