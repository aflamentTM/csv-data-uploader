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
//  DB Table on Plugin Activation
register_activation_hook(__FILE__, "cdu_create_table");
function cdu_create_table()
{
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $table_name = $table_prefix . "students_data";

    $table_collate = $wpdb->get_charset_collate();

    $sql_command = "
        CREATE TABLE `" . $table_name . "` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) DEFAULT NULL,
        `email` varchar(50) DEFAULT NULL,
        `age` int(5) DEFAULT NULL,
        `phone` varchar(30) DEFAULT NULL,
        `photo` varchar(120) DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) " . $table_collate . "
    ";
    require_once(ABSPATH . "/wp-admin/includes/upgrade.php");
    dbDelta($sql_command);
}

// To add Script file
add_action("wp_enqueue_scripts", "cdu_add_scripts_file");
function cdu_add_scripts_file()
{
    wp_enqueue_script("cdu-script-js", plugin_dir_url(__FILE__) . "assets/script.js", array("jquery"));
    wp_localize_script("cdu-script-js", "cdu_object", array(
        "ajax_url" => admin_url("admin-ajax.php")
    ));
}

// Capture ajax request 
add_action("wp_ajax_cdu_submit_form_data", "cdu_ajax_handler");
add_action("wp_ajax_nopriv_cdu_submit_form_data", "cdu_ajax_handler");

function cdu_ajax_handler()
{
    if ($_FILES['csv_data_file']) {
        $csv_File = $_FILES['csv_data_file']['tmp_name'];
        $handle = fopen($csv_File, "r");
        if ($handle) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000,";")) !== FALSE) {
                global $wpdb;
                $table_prefix = $wpdb->prefix;
                $table_name = $table_prefix . "students_data";
                if ($row == 0) {
                    $row++;
                    continue;  // Skip the first row
                }

                $wpdb->insert($table_name, array(
                    "name" => $data[1],
                    "email" => $data[2],
                    "age" => $data[3],
                    "phone" => $data[4],
                    "photo" => $data[5]
                ));
                
                
                }
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Data has been uploaded successfully" ));
                fclose($handle);
        } else {
            echo json_encode(array(
                "status" => 1,
                "message" => "Hello From csv Data uploader"
            ));
        }
        exit;
    }
}
