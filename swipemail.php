<?php

defined( 'ABSPATH' ) or die( 'No direct access!' );

/*
Plugin Name: SwipeMail
Plugin URI: http://swipemail.io/swipemail-wordpress-plugin/
Description: Get the email subscribtion form on front-end of your wordpress and connect it with your SwipeMail account
Version: 1.0
Author: Imran Shaikh
Author URI: http://swipemail.io/
*/

/*
  LICENSE:
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
  
  Please refer to license.txt for more details.
 */

add_action( 'wp_ajax_swipemail_plugin_post_data', 'swipemail_plugin_post_data' );
add_action( 'wp_ajax_nopriv_swipemail_plugin_post_data', 'swipemail_plugin_post_data' );

function swipemail_plugin_post_data(){
    $swipemail_plugin_email = $_POST['swipemail-plugin-email'];
    //file_put_contents('/tmp/swipemail_plugin_post_data.txt',"post data called email($swipemail_plugin_email) at ".date('Y-m-d h:i:s',time())."\n",FILE_APPEND);
    ob_clean();
    //echo $swipemail_plugin_email;
    //now call our api here
    $swipemail_username = strtolower(get_option('swipemail_username'));
    $swipemail_password = get_option('swipemail_password');
    $swipemail_listid = get_option('swipemail_listid');
    //example
    //http://appbox5.swipemail.in/<username>/admin/?page=call&pi=restapi&login=admin&password=<YOUR_PASSWORD>&cmd=subscribe&email=<email>&lists=<listid>
    $swipemail_url = 'http://appbox5.swipemail.in/'.$swipemail_username.'/admin/'
                    . '?page=call&pi=restapi'
    //$swipemail_params = 'page=call&pi=restapi'
                    . '&login=admin&password='.$swipemail_password
                    . '&cmd=subscribe&email='.$swipemail_plugin_email
                    . '&lists='.$swipemail_listid;
                    
                    
    //echo "swipemail_username($swipemail_username)";
    //echo "swipemail_password($swipemail_password)";
    //echo "swipemail_listid($swipemail_listid)";
    //echo "swipemail_url($swipemail_url)";
    //echo "swipemail_params($swipemail_params)";
    
    //make api call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$swipemail_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$swipemail_params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec ($ch);
    curl_close ($ch);
    //echo "server_output($server_output)";
    
    /**
    //Error response
    {
        "status":"error",
        "type":"Error",
        "data":{
            "code":"23000",
            "message":"SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'imran@swipemail.io' for key 'email'"
        }
    }    
    //Success response
    {
        "status":"success",
        "type":"Subscriber",
        "data":{
            "id":"23162",
            "email":"test123@sink.swipemail.in",
            "confirmed":"0",
            "blacklisted":"0",
            "optedin":"0",
            "bouncecount":"0",
            "entered":"2017-11-21 04:18:04",
            "modified":"2017-11-21 04:18:04",
            "uniqid":"b2e4fd1f9a26e21e86565e95a8dea026",
            "htmlemail":null,
            "subscribepage":"0",
            "rssfrequency":null,
            "password":null,
            "passwordchanged":null,
            "disabled":"0",
            "extradata":null,
            "foreignkey":null
        }
    }
    */
    
    //echo "Email added successfully!";
    echo $server_output;
    wp_die();
}


//Hooks -- start
if ( is_admin() ){ // admin actions
    add_action('admin_menu', 'swipemail_plugin_menu');
    add_action( 'admin_init', 'register_swipemail_plugin_settings' );
} else {
  // non-admin enqueues, actions, and filters
}

function register_swipemail_plugin_settings() { // whitelist options
  register_setting( 'swipemailplugin-group', 'swipemail_username' );
  register_setting( 'swipemailplugin-group', 'swipemail_password' );
  register_setting( 'swipemailplugin-group', 'swipemail_listid' );
}

function swipemail_plugin_menu() {
    //add_menu_page('Page Title', 'Menu Title', 'Capability', 'menu-slug', 'name_of_function', 'icon','position');
    add_menu_page('SwipeMail Plugin Settings', 'Swipemail Plugin Settings', 'administrator', 'swipemail-settings', 'swipemail_plugin_settings', 'dashicons-admin-generic');
	//add_menu_page( 'Swipemail Plugin Configs');
}

function swipemail_plugin_settings() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
<h1>SwipeMail Plugin Settings</h1>
<div class="wrap">
    <p>Please set the below config before using the plugin it:</p>
    <form method="post" action="options.php"> 
    <?php 
        settings_fields( 'swipemailplugin-group' );
        do_settings_sections( 'swipemailplugin-group' );
    ?>
    
    <table class="form-table">
        <tr valign="top">
        <th scope="row">SwipeMail Username</th>
        <td><input type="text" name="swipemail_username" value="<?php echo esc_attr( get_option('swipemail_username') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">SwipeMail Password</th>
        <td><input type="text" name="swipemail_password" value="<?php echo esc_attr( get_option('swipemail_password') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">SwipeMail List ID</th>
        <td><input type="text" name="swipemail_listid" value="<?php echo esc_attr( get_option('swipemail_listid') ); ?>" /></td>
        </tr>

    </table>
    
    <?php submit_button(); ?>
    </form>
</div>
	<?php
}

//Hooks -- end

add_shortcode( 'swipemail_subscribe_form', 'swipemail_plugin_show_form' );

//all functions below
function swipemail_plugin_show_form(){
     //print_r($_SERVER);exit; 
    ?>    
   <style>
   #swipemail-plugin-message-box-success{
    color:green;
   }
   #swipemail-plugin-message-box-failed{
    color:red;
   }
   </style>
    
   <script type="text/javascript">
   
   function swipemail_plugin_submit_form(){
        //set all none
        document.getElementById('swipemail-plugin-message-box-success').style.display = "none";
        document.getElementById('swipemail-plugin-message-box-failed').style.display = "none";
        document.getElementById('swipemail-plugin-message-box-wait').style.display = "none";

        if(swipemail_plugin_validate_email()){
            //document.getElementById('subscriber-email').value();
            //alert("email is valid");
            if(!swipemail_plugin_post_data()){
                //put on wait status
                document.getElementById('swipemail-plugin-message-box-wait').style.display = "block";
                document.getElementById('swipemail-plugin-message-box-success').style.display = "none";                
                document.getElementById('swipemail-plugin-message-box-failed').style.display = "none";
            }
        }
        else{
            document.getElementById('swipemail-plugin-message-box-failed').style.display = "block";
            document.getElementById('swipemail-plugin-message-box-failed-contents').innerHTML = "Error: Email is not valid!";
            return false;
        }
   }
   
   function swipemail_plugin_validate_email(){

		 var email_id=document.getElementById('swipemail-plugin-email').value;

		 var filter = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;

		 valid= String(email_id).search (filter) != -1;

		if( ! valid ) {

			//alert('Please enter a valid email address');

			return false;
		} else {
			return true;
		}
   }

   function swipemail_plugin_post_data(){
        var email = document.getElementById('swipemail-plugin-email').value;
        //alert("email("+email+")");
        //alert("posting data: email("+email+") url("+ajax_object.ajax_url+")");
	    var data = {
		    'action': 'swipemail_plugin_post_data',
		    'swipemail-plugin-email': email
	    };
	    //var url = "<?php print 'http://'.  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>";
	    //var url = "<?php print 'http://'.  $_SERVER['HTTP_HOST']  . strtok($_SERVER["REQUEST_URI"],'?') ?>";
	    //$url=strtok($_SERVER["REQUEST_URI"],'?');
	    //alert("url("+url+")");
	    //var url = 'http://localhost/wordpress/4.9/wp-admin/admin-ajax.php/';
	    //var url = "<?php print 'http://'.  $_SERVER['HTTP_HOST']  . '/wp-admin/admin-ajax.php/'; ?>";
	    var url = "<?php print get_option('siteurl')  . '/wp-admin/admin-ajax.php/'; ?>";
	    //alert("url("+url+")");
	    
	    
	    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
	    jQuery.post(url, data, function(response) {
		    //alert('Got this from the server: ' + response);
		    response = JSON.parse(response);
		    if(response.status == 'success'){
                document.getElementById('swipemail-plugin-message-box-wait').style.display = "none";
                document.getElementById('swipemail-plugin-message-box-success').style.display = "block";                
                document.getElementById('swipemail-plugin-message-box-success-contents').innerHTML = "Success: email submitted successfully! Please check your inbox for confirmation link.";
                document.getElementById('swipemail-plugin-message-box-failed').style.display = "none";
		    }
		    else{
		        var error = "Error: Oops! Some Technical error in submit!";
		        if(response.data.code == '23000'){
		            error = "Error: You have already subscribed or submitted your email!";
		        }
                document.getElementById('swipemail-plugin-message-box-wait').style.display = "none";
                document.getElementById('swipemail-plugin-message-box-success').style.display = "none";
                document.getElementById('swipemail-plugin-message-box-failed').style.display = "block";
                document.getElementById('swipemail-plugin-message-box-failed-contents').innerHTML = error;
		    }
	    });
   }


    /*
    jQuery(document).ready(function($) {
	    var data = {
		    'action': 'swipemail_plugin_post_data',
		    'email': ajax_object.we_value      // We pass php values differently!
	    };
	    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
	    jQuery.post(ajax_object.ajax_url, data, function(response) {
		    alert('Got this from the server: ' + response);
	    });
    });
    */
   

   </script>
    
    <form id="swipemail-plugin-subscribe-form" onsubmit="return swipemail_plugin_submit_form()">
    <img src="">
    <table class="form-table">
        <tr id="swipemail-plugin-message-box-wait" style="display:none;">
            <td id="swipemail-plugin-message-box-wait-contents">
                <img src="<?php print get_option('siteurl')  . '/wp-content/plugins/swipemail/ajax-loader.gif'; ?>" />
            </td>
        </tr>
        <tr id="swipemail-plugin-message-box-success" style="display:none;">
            <td id="swipemail-plugin-message-box-success-contents"></td>
        </tr>
        <tr id="swipemail-plugin-message-box-failed" style="display:none;">
            <td id="swipemail-plugin-message-box-failed-contents"></td>
        </tr>
        <tr>
            <td><input id="swipemail-plugin-email" name="swipemail-plugin-email" placeholder="email address" /></td>
        </tr>
        <tr>
            <td>
                <!-- <input id="swipemail-plugin-submit" type="submit" value="Submit" /> -->
                <input id="swipemail-plugin-submit" type="button" value="Subscribe" onclick="swipemail_plugin_submit_form()" />
            </td>
        </tr>
    </table>
    </form>

    <?php
}

?>
