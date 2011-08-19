<?php
/*************************************************************************
 * Plugin Name: Genesis Simple Defaults
 * Author: Hit Reach
 * Author URI: http://www.hitreach.co.uk/
 * Description: Allows the settings of some defaults for Genesis themes
 * Version: 1.0.0
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
define("GSD_URL", WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));
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
	<h1 style='margin-bottom:5px;'>Genesis Simple Defaults</h1>
	<h2 style='margin-top:0px;'>By <span style='color:#172951;'>Hit</span> <span style='color:#7D1316;'>Reach</span></h2>
	<div style='width:1010px;'>
		<div style='width:400px; float:right;'><?php GSD_appeal(); ?></div>
		<div style='width:580px;float:left; background:white; border:1px #aaa solid;padding:10px;'>
			<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<table class="form-table" width="100%">
				<tr valign="top">
					<th scope="row"><label for="SD_HR_OPTIONS[comment-title]">Comment Reply Title: </label></th>
					<td><input type="text" name="SD_HR_OPTIONS[comment-title]" id="SD_HR_OPTIONS[comment-title]"  style='width:88%; cursor:pointer;' value="<?php echo $title ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="SD_HR_OPTIONS[thumb_70]">Default Thumbnail Image Url <br/><span style='color:red'>70px x 70px</span>: </label></th>
					<td><input type="text" name="SD_HR_OPTIONS[thumb_70]" id="SD_HR_OPTIONS[thumb_70]"  style='width:88%; cursor:pointer;' value="<?php echo $thumb_70 ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="SD_HR_OPTIONS[thumb_110]">Default Thumbnail Image Url <br/><span style='color:red'>110px x 110px</span>: </label></th>
					<td><input type="text" name="SD_HR_OPTIONS[thumb_110]" id="SD_HR_OPTIONS[thumb_110]"  style='width:88%; cursor:pointer;' value="<?php echo $thumb_110 ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="SD_HR_OPTIONS[thumb_150]">Default Thumbnail Image Url <br/><span style='color:red'>150px x 150px</span>: </label></th>
					<td><input type="text" name="SD_HR_OPTIONS[thumb_150]" id="SD_HR_OPTIONS[thumb_150]"  style='width:88%; cursor:pointer;' value="<?php echo $thumb_70 ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="SD_HR_OPTIONS[favicon]"> Favicon <br/> Set empty to remove : </label></th>
					<td><input type="text" name="SD_HR_OPTIONS[favicon]" id="SD_HR_OPTIONS[favicon]"  style='width:88%; cursor:pointer;' value="<?php echo $favicon ?>" /></td>
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
		</div>
	</div>
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

function GSD_appeal(){
	?>
	<div style='float:right; display:inline; width:379px; padding:10px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-webkit-box-shadow: #666 2px 2px 5px;-moz-box-shadow: #666 2px 2px 5px;box-shadow: #666 2px 2px 5px;background: #ffff00;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#ffff00), to(#ffffcc));background: -moz-linear-gradient(#ffff00, #ffffcc);background: linear-gradient(#ffff00, #ffffcc);'>
        <span style='font-size:1em; color:#999; display:block; line-height:1.2em;'><strong>Developed by <a href='http://www.hitreach.co.uk' target="_blank" style='text-decoration:none;'>Hit Reach</a></strong><a href='http://www.hitreach.co.uk' target="_blank" style='text-decoration:none;'></a></span>
        <span style='font-size:1em; color:#999; display:block; line-height:1.2em;'><strong>Check out our other <a href='http://www.hitreach.co.uk/services/wordpress-plugins/' target="_blank" style='text-decoration:none;'>Wordpress Plugins</a></strong><a href='http://www.hitreach.co.uk/services/wordpress-plugins/' target="_blank" style='text-decoration:none;'></a></span>
        <span style='font-size:1em; color:#999; display:block; line-height:1.2em;'><strong>Version: 1.0.0 <a href='http://www.hitreach.co.uk/wordpress-plugins/simple-defaults/' target="_blank" style='text-decoration:none;'>Support, Comments &amp; Questions</a></strong></span>
      <hr/>
        <h2>Please help! We need your support...</h2>
        <p>If this plugin has helped you, your clients or customers then please take a moment to 'say thanks'. </p>
        <p>By spreading the word you help increase awareness of us and our plugins which makes it easier to justify the time we spend on this project.</p>
        <p>Please <strong>help us keep this plugin free</strong> to use and allow us to provide on-going updates and support.</p>
        <p>Here are some quick, easy and free things you can do which all help and we would really appreciate.</p>
        <ol>
            <li>
                <strong>Promote this plugin on Twitter</strong><br/>
                <a href="http://twitter.com/home?status=I'm using the Genesis Simple Defaults plugin by @hitreach and it rocks! You can download it here: http://bit.ly/oNpcI2" target="_blank">
                <img src='<?php echo GSD_URL;?>/twitter.gif' border="0" width='55' height='20'/>
                </a><br/><br/>
        </li>
            <li>
                <strong>Link to us</strong><br/>
                By linking to <a href='http://www.hitreach.co.uk' target="_blank">www.hitreach.co.uk</a> from your site or blog it means you can help others find the plugin on our site and also let Google know we are trust and link worthy which helps our profile.<br/><br/>
                </li>
          <li>
                <strong>Like us on Facebook</strong><br/>
                Just visit <a href='http://www.facebook.com/webdesigndundee' target="_blank">www.facebook.com/webdesigndundee</a> and hit the 'Like!' button!<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="http://www.facebook.com/webdesigndundee" send="true" width="350" show_faces="false" action="like" font="verdana"></fb:like><br/><br/>
          </li>
            <li>
                <strong>Share this plugin on Facebook</strong><br/>
                <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="http://www.hitreach.co.uk/wordpress-plugins/simple-defaults/" send="true" width="350" show_faces="false" action="recommend" font="verdana"></fb:like>
                Share a link to the plugin page with your friends on Facebook<br/><br/>
            </li>
            <li>
                <strong>Make A Donation</strong><br/>
                Ok this one isn't really free but hopefully it's still a lot cheaper than if you'd had to buy the plugin or pay for it to be made for your project. Any amount is appreciated

                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                    <input type="hidden" name="cmd" value="_donations">
                    <input type="hidden" name="business" value="admin@hitreach.co.uk">
                    <input type="hidden" name="lc" value="GB">
                    <input type="hidden" name="item_name" value="Hit Reach">
                    <input type="hidden" name="item_number" value="Genesis Simple Defaults">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="currency_code" value="GBP">
                    <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
                    <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
            </li>
        </ol>
    </div>
<?php }
?>