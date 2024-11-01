<?php
/**
 * Plugin Name: StayLogged
 * Description: Automatically logs out users after any period of inactivity that you want; so you can keep users logged in for a shorter or longer time
 * Version: 1.0
 * Author: Vanda Nojan
 * License: GPLv2 or late
 */

// Add settings page
function inactivity_logout_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php _e( 'Inactivity Logout Settings', 'inactivity-logout' ); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'inactivity_logout_options' ); ?>
            <?php do_settings_sections( 'inactivity_logout_options_page' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Logout Time Duration (in seconds)', 'inactivity-logout' ); ?></th>
                    <td><input type="number" name="inactivity_logout_duration" min="60" value="<?php echo esc_attr( get_option( 'inactivity_logout_duration', 3600 ) ); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings and menu item
function inactivity_logout_register_settings() {
    register_setting( 'inactivity_logout_options', 'inactivity_logout_duration', 'intval' );
    add_options_page( __( 'Inactivity Logout Settings', 'inactivity-logout' ), __( 'Inactivity Logout', 'inactivity-logout' ), 'manage_options', 'inactivity_logout_options_page', 'inactivity_logout_settings_page' );
}
add_action( 'admin_menu', 'inactivity_logout_register_settings' );

// Main function to logout users after inactivity
function custom_inactivity_logout() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $last_active = get_user_meta( $user->ID, '_last_active', true );
        $current_time = current_time( 'timestamp' );
        $timeout_duration = apply_filters( 'custom_inactivity_timeout_duration', get_option( 'inactivity_logout_duration', 3600 ) ); // Get the logout time duration from the settings
        if ( $last_active && ( $current_time - $last_active ) > $timeout_duration ) {
            wp_logout();
        }
        update_user_meta( $user->ID, '_last_active', $current_time );
    }
}
add_action( 'init', 'custom_inactivity_logout' );
