<?php
function icomply_setup(){
    global $wpdb;
    $table_name = $wpdb->prefix . "icomplydetails";

   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        icomply_username varchar(255) NOT NULL,
        icomply_password tinytext NOT NULL,
        icomply_key varchar(64) NOT NULL,
        icomply_email varchar(255) NOT NULL,
        icomply_wp_resturl MEDIUMTEXT NOT NULL,
        userid bigint(20) NOT NULL,
        PRIMARY KEY id (id) );";        
        $wpdb->query($sql);
        add_option('icomplysetup_created_table', true);
    }
    add_option('my_plugin_do_activation_redirect', true);
}

function icomply_uninstall(){
     
    global $wpdb;
    $table_name = $wpdb->prefix . "icomplydetails";  
    if (get_option('icomplysetup_created_table', false)) {
        delete_option('icomplysetup_created_table');
        $sql = "DROP TABLE IF EXISTS $table_name;";
        $wpdb->query($sql);
    }
    
}
?>