<?php
/**
Hook into WordPress
*/
add_action('admin_head', 'marker_select_box_css');

function marker_select_box_css() {
    echo "<link type='text/css' rel='stylesheet' href='" .  LEAFLET_PLUGIN_URL . 'leaflet-dist/marker_select_box.css' . "' />";
}

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
    global $wpdb;

    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    
    $marklist = $wpdb->get_results("
            (SELECT l.id, l.name as 'name', l.createdon, 'layer' as 'type' FROM $table_name_layers as l WHERE l.id != '0')
            UNION
            (SELECT m.id, m.markername as 'name', m.createdon, 'marker' as 'type' FROM $table_name_markers as m WHERE  m.id != '0')
            order by createdon DESC", ARRAY_A);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Insert/Edit link</title>
        
        <script type='text/javascript' src='/wp-admin/load-scripts.php?c=1&load=jquery'></script>
        <script type='text/javascript' src='/wp-includes/js/tinymce/tiny_mce_popup.js'></script>
        <style>
            #msb_insertMarkerSC{
                float: right;
            }
            #msb_header_description{
                color:#666666;

            }
            #msb_serchContainer{
                padding-left: 40px;
                margin: 5px 0;
            }
            #msb_listContainer{
                height: 350px;
                width:100%;
                overflow-y: scroll;


            }
            #msb_listContainer div{
                border: 1px solid #ccc;
            }
            input.button-primary, button.button-primary, a.button-primary {
                background: url("/wp-admin/images/button-grad.png") repeat-x scroll left top #21759B;
                border-color: #298CBA;
                color: #FFFFFF;
                font-weight: bold;
                text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
                   -moz-box-sizing: content-box;
                border-radius: 11px 11px 11px 11px;
                border-style: solid;
                border-width: 1px;
                cursor: pointer;
                font-size: 12px !important;
                line-height: 13px;
                padding: 3px 8px;
                text-decoration: none;
            }
            #msb_listContainer .list_item{
                border-top: none;
                
            }
        </style>
</head>
<body>

<span id="msb_header_description">Please select the maps you would like to include</span>
<div id="msb_serchContainer">Search <input type="text" /></div>
<div id="msb_listContainer">
    <div id="msb_listHint" >Please select the maps you would like to include</div>
    <?php foreach($marklist as $one):?>
    <div class="list_item">
        <span><?php echo $one['name']?></span><span><?php echo $one['createdon']?></span>
        <input type="hidden" value="<?php echo $one['type']?>" name="msb_type">
        <input type="hidden" value="<?php echo $one['id']?>" name="msb_id">
    </div>
    <?php endforeach; ?>  
</div>

<a href="#" id="msb_cancel">Cancel</a>
<input class="button-primary" type="button" href="#" id="msb_insertMarkerSC" value="Add shortcode" />       

<script type="text/javascript">
(function($){
    function selectMarkerBox(){
        this.markerID = '';
        this.mapsmarkerType = '';
        //this.shortCode = '[mapsmarker '+ this.mapsmarkerType +'="'+ this.markerID +'"]';
        self = this;
    }
        
    selectMarkerBox.prototype = {
        init : function(){
            $('.map_marker').live('click', function(){
                e.preventDefault();
                console.log( $(this).text() );
            })

            $('#msb_insertMarkerSC').live('click', function(e){
                e.preventDefault();
                self.insert();
                self.close();
            })
            
            $('#msb_cancel').live('click', function(e){
                e.preventDefault();
                self.close();
            })
            
            $('.list_item').live('click', function(e){
                e.preventDefault();
                
                var id = $(this).find('input[name="msb_id"]').val();
                var type = $(this).find('input[name="msb_type"]').val(); 
                
                self.setMarkerID(id)
                self.setMarkerType(type);

            })
        },        
        setMarkerID : function(id) {
            this.markerID = id;
        },
        setMarkerType : function(type) {
            switch (type)
            {
                case 'layer': 
                    this.mapsmarkerType = 'layer';
                    break;
                case 'marker': 
                    this.mapsmarkerType = 'marker';
                    break;
            }
        },
        getShortCode : function(){
          return '[mapsmarker '+ this.mapsmarkerType +'="'+ this.markerID +'"]';  
        },
        insert : function() {
            tinyMCEPopup.editor.execCommand('mceInsertContent', false, this.getShortCode());
        },
        
        insertMarker : function() {
            return;
        },
        
        insertList : function() {
            return;
        },
        close : function() {
            tinyMCEPopup.close();        
        }
        
    }
    
    markerBox = new selectMarkerBox();
    markerBox.init();
})(jQuery)
</script>

</body>
</html>
<?php    
  exit;
}


?>
