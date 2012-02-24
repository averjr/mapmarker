<?php
/**
Hook into WordPress
*/

add_action('init', 'mm_shortcode_button');

/**
Create Our Initialization Function
*/
function mm_shortcode_button() {

   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
     return;
   }

   if ( get_user_option('rich_editing') == 'true' ) {
     add_filter( 'mce_external_plugins', 'add_plugin' );
     add_filter( 'mce_buttons', 'register_button' );
   }

}

/**
Register Button
*/
function register_button( $buttons ) {
 array_push( $buttons, "|", "mm_shortcode" );
 return $buttons;
}

/**
Register TinyMCE Plugin
*/
function add_plugin( $plugin_array ) {
   $plugin_array['mm_shortcode'] = plugins_url( 'leaflet-dist/mm_shortcode.js' , __FILE__ );
   return $plugin_array;
}


add_action('wp_ajax_get_my_form',  'get_my_form');

function get_my_form(){
?>

<div class="map_marker">1</div>
<div class="map_marker">2</div>
<div class="map_marker">3</div>
<a href="#" id="insertMarkerSC">Create Shortcode</a>                    

<?php    
  exit;
}


?>
