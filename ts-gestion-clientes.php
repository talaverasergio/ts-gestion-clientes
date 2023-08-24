<?php
/**
* Plugin Name:       TS Gestión de Clientes
* Plugin URI:        https://github.com/talaverasergio/ts-gestion-clientes
* Description:       Permite la gestion de clientes de un negocio, tales como asistencias y control de pagos.
* Version:           0.1
* Author:            Sergio D. Talavera
* Author URI:        https://github.com/talaverasergio
* License:           GPLv3
* License URI:       https://www.gnu.org/licenses/gpl-3.0.html
**/

define( 'TS_GESTION_CLIENTES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TS_GESTION_CLIENTES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// No olvidar actualizar el numero de version al actualizar el schema
global $ts_gestion_clientes_db_version;
$ts_gestion_clientes_db_version = 0.1;
add_option('ts_gestion_clientes_db_version', $ts_gestion_clientes_db_version);


// Funcion que crea nuestra base de datos al activar el plugin
function ts_crear_database(){
    
    global $wpdb;

    $users_table_name = $wpdb->prefix . 'ts_gestion_db_users';
    $attendance_table_name = $wpdb->prefix . 'ts_gestion_db_attendance';
    $payment_table_name = $wpdb->prefix . 'ts_gestion_db_payment';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $users_table_name (
        user_id int(11) NOT NULL,
        PRIMARY KEY  (user_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    $wpdb->query("CREATE TABLE $attendance_table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        fecha_asistencia DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY  (user_id) REFERENCES $users_table_name(user_id)
    ) $charset_collate;"
    );

    $wpdb->query("CREATE TABLE $payment_table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        monto INT(11) NOT NULL,
        detalle TINYTEXT,
        fecha_pago DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY  (user_id) REFERENCES $users_table_name(user_id)
    ) $charset_collate;"
    );
}
    
register_activation_hook( __FILE__, 'ts_crear_database' );

//Eliminamos la database al desinstalar
function ts_remover_database(){
    
    global $wpdb;
    
    $users_table_name = $wpdb->prefix . 'ts_gestion_db_users';
    $attendance_table_name = $wpdb->prefix . 'ts_gestion_db_attendance';
    $payment_table_name = $wpdb->prefix . 'ts_gestion_db_payment';    
    
    $sql = "DROP TABLE IF EXISTS $users_table_name";
    $wpdb->query($sql);

    $sql = "DROP TABLE IF EXISTS $attendance_table_name";
    $wpdb->query($sql);

    $sql = "DROP TABLE IF EXISTS $payment_table_name";
    $wpdb->query($sql);

    delete_option('ts_gestion_clientes_db_version');
}

register_deactivation_hook(__FILE__, 'ts_remover_database');?>