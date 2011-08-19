<?php
include_once("../../../wp-config.php");
include_once("../../../wp-load.php");
include_once("../../../wp-includes/wp-db.php");
global $wpdb;
$refer = $_SERVER['HTTP_REFERER'];
if(!isset($_POST['upload-favicon'])){
	if ( !wp_verify_nonce( $_POST['upload-favicon'], plugin_basename(__FILE__) )) {header("location:".$refer."&nonceless");}
}
else{
	if(!isset($_FILES['iconImage']) || !defined ('ABSPATH')){header("location:".$refer."&imageless");}
	$chosenUrl = "favicon";
	if(!is_dir("../../uploads/" . $chosenUrl)){
		mkdir("../../uploads/" . $chosenUrl);
	}
	$image = $_FILES['iconImage'];
	
	if( ($image['type'] != "image/gif") && ($image['type'] != "image/jpeg") && ($image['type'] != "image/pjpeg") && ($image['type'] != "image/ico") && ($image['type'] != "image/vnd.microsoft.icon") && ($image['type'] != "image/png") ){ header("location:".$refer."&mistype=".$image['type']); }
	
	$targetPath = "../../uploads/" . $chosenUrl . "/" . basename( $image['name']);
 	move_uploaded_file($image['tmp_name'], $targetPath);
	 $favDif = get_bloginfo('url')."/wp-content/uploads/favicon/". $image["name"];
	 if(get_option("SD_HR_OPTIONS")){
		if(is_serialized(get_option("SD_HR_OPTIONS"))){
			$option = unserialize(get_option("SD_HR_OPTIONS")); }
		else{$option = get_option("SD_HR_OPTIONS");}						
		$option['favicon'] = $favDif;
		update_option("SD_HR_OPTIONS", $option);
		header("location:".$refer."&success");
	}
}
header("location:".$refer."&err");
?>