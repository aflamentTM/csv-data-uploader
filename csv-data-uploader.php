<?php 
/**
 * Plugin Name: CSV Data Uploader Plugin
 * Description: This plugin will upload csv data to DB Table
 * Author: Arnaud Flament
 * Version: 1.0
 * Author URI: 
 * Plugin URI: https://github.com/aflamentTM/csv-data-uploader
 */
define("CDU_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));

 add_shortcode("csv-data-uploader", "cdu_display_uploader_form");

 function cdu_display_uploader_form() 
 {
    //  Start PHP buffer
    ob_start();
    include_once CDU_PLUGIN_DIR_PATH . "/template/cdu_form.php"; // put all contents in the buffer
    //  Read buffer
    $template = ob_get_contents();
    // Clean buffer
    ob_end_clean();

    return $template;
 }