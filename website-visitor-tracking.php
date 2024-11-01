<?php
/**
 * Plugin Name: WorksIQ Website Visitor Tracking
 * Plugin URI: http://www.worksiq.com/wordpress-website-visitor-tracking-plugin/
 * Description: For embedding links to the WorksIQ website visitor tracking code into your WordPress site.
 * Version: 1.0.4
 * Author: Cirra Technologies Limited
 * Author URI: http://www.worksiq.com
 * License: GPLv2
 * Copyright 2014  Cirra Technologies Limited  (email : support@worksiq.com)
 */

class WebsiteVisitorTracking {

  static function header()  {
	// Embed the wiq_tid in the <head>. 
	echo "\n<!-- WorksIQ Plugin Start -->";
    echo "\n<script type=\"text/javascript\">";
    // How to get the wiq_tid from the user??

    $opt_name = 'mt_wiq_tid';
    $opt_val = get_option( $opt_name );

    echo "\nvar wiq_tid = \"" . $opt_val . "\";";
    echo "\nvar cookie_consent = 'Accepted';";
    echo "\nwiq_consent = true;";
    echo "\n</script>";
	echo "\n<!-- WorksIQ Plugin End -->\n\n";

    return;
  }

  static function footer()  {
    // Embed the landing page code in the Footer. 
    echo "\n<!-- WorksIQ Plugin Start -->\n<script type=\"text/javascript\" src=\"https://live.worksiq.com/WorksIQTracking/Scripts/Tracking/LandingPage-1.2.4.min.js\"></script>";

    echo "\n<!--The remainder of the snippet is only required on pages with forms -->";

    echo "\n<script type=\"text/javascript\">";
    echo "\n    function foundform()";
    echo "\n    {";
    echo "\n    var today = new Date();";
    echo "\n    today.setTime(today.getTime());";
    echo "\n    t = new WIQ_Cookie(\"wiq_t\", null, null, \"\").Load();";
    echo "\n    if (t != null) {";
    echo "\n        var base = new WIQ_BaseCookie(t);";
    echo "\n        var sessionCookie = WIQ_GetSessionCookie();";
    echo "\n        if (sessionCookie != null) {";
    echo "\n            var transportData = WIQ_GetTransportData(base.getId(), sessionCookie);";
    echo "\n            var historyVisitCookies = WIQ_GetHistoryCookies();";
    echo "\n            var visitCookie = WIQ_AddVisitCookie(base, today, true);";
    echo "\n        document.getElementsByName('cookiedata').item(0).value=\"data: \" + transportData + \", visitcookie: \" + visitCookie + \", historyvisitcookies: \" + historyVisitCookies;";
    echo "\n        WIQ_ClearHistoryCookies(0);";
    echo "\n                                    }";
    echo "\n                    } else { document.getElementsByName('cookiedata').item(0).value=\"data: \"; }";                   
    echo "\n    }";
    echo "\n</script>";
    echo "\n<script type=\"text/javascript\">"; 
    echo "\n    if(document.getElementsByName('cookiedata').length > 0) {";
    echo "\n    window.setTimeout(foundform,1000);";
    echo "\n    }";
    echo "\n</script>";
    echo "\n<!-- WorksIQ Plugin End -->\n\n";


    return;
  }

}

function worksiq_plugin_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

   // variables for the field and option names 
    $opt_name = 'mt_wiq_tid';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_wiq_tid';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'WorksIQ Website Visitor Tracking Settings', 'menu-test' ) . "</h2>";
    echo '<div class="wrap">';
    echo '<p>Please enter your Tenant Code. This can be found in your WorksIQ system under Admin >> Configuration >> General Settings </p>';
    echo '</div>';

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Tenant ID:", 'menu-test' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>
<?php
}

function my_plugin_menu() {
    add_options_page( 'WorksIQ Plugin Options', 'WorksIQ', 'manage_options', 'worksiq-website-visitor-tracking', 'worksiq_plugin_options' );
}

add_action('wp_head', array('WebsiteVisitorTracking', 'header'));
add_action('wp_footer', array('WebsiteVisitorTracking', 'footer'), 500);
add_action('admin_menu', 'my_plugin_menu');

?>