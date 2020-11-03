<?php
/*
Plugin Name: YachtWorld API
description: a plugin to integrate with the Yachtworld System
Version: 1.5
*/

include_once('public/YachtWorldPublic.php');
include_once('admin/YachtWorldAdmin.php');
include_once('YachtWorldShortCode.php');

if( is_admin() ) {
    $my_settings_page = new YachtWorldAdmin();
}

$yachtShortCode = new YachtWorldShortCode();


?>