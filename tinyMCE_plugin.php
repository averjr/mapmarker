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


add_action('wp_ajax_get_mm_list',  'get_mm_list');

function get_mm_list(){
    global $wpdb;

    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    
    $l_condition = isset($_GET['q']) ? "AND l.name LIKE '%" . mysql_real_escape_string($_GET['q']) . "%'" : '';
    $m_condition = isset($_GET['q']) ? "AND m.markername LIKE '%" . mysql_real_escape_string($_GET['q']) . "%'" : '';
    
    
    $marklist = $wpdb->get_results("
            (SELECT l.id, l.name as 'name', l.createdon, 'layer' as 'type' FROM $table_name_layers as l WHERE l.id != '0' $l_condition)
            UNION
            (SELECT m.id, m.markername as 'name', m.createdon, 'marker' as 'type' FROM $table_name_markers as m WHERE  m.id != '0' $m_condition)
            order by createdon DESC LIMIT 30", ARRAY_A);

    if(isset($_GET['q']) ){
        buildMarkersList($marklist);
        exit();
    }
?>


<!DOCTYPE html>
<html>
<head>
	<title>Insert/Edit link</title>
        
        <script type='text/javascript' src='/wp-admin/load-scripts.php?c=1&load=jquery'></script>
        <script type='text/javascript' src='/wp-includes/js/tinymce/tiny_mce_popup.js'></script>
        <script type='text/javascript' src='<?php echo plugins_url( 'leaflet-dist/mm_shortcode.js' , __FILE__ ) ?>'></script>
        <link   rel='stylesheet'       href='<?php echo plugins_url( 'leaflet-dist/marker_select_box.css' , __FILE__ ) ?>' type='text/css' media='all' />

</head>
<body>

<span id="msb_header_description">Please select the maps you would like to include</span>
<div id="msb_serchContainer">Search <input type="text" name="q" id="msb_serch"/></div>
<div id="msb_listContainer">
    <div id="msb_listHint" >Please select the maps you would like to include</div>
    <?php buildMarkersList($marklist); ?>
</div>

<a href="#" id="msb_cancel">Cancel</a>
<input class="button-primary" type="button" href="#" id="msb_insertMarkerSC" value="Add shortcode" />       

<script type="text/javascript">
(function($){
    var selectMarkerBox = {
        markerID : '',
        mapsmarkerType : '',
        
        init : function(){
            var self = selectMarkerBox;
            
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
                
                $('.list_item.active').removeClass('active');
                $(this).addClass('active');
                
                self.setMarkerID(id)
                self.setMarkerType(type);

            })
            
            $('#msb_serch').live('keyup', function(){
                $.post('/wp-admin/admin-ajax.php?action=get_mm_list&q='+$(this).val(), function(data){
                        $('.list_item').remove();
                        $('#msb_listContainer').append(data);
                })
            })
        },        
        setMarkerID : function(id) {
            selectMarkerBox.markerID = id;
        },
        setMarkerType : function(type) {
            switch (type)
            {
                case 'layer': 
                    selectMarkerBox.mapsmarkerType = 'layer';
                    break;
                case 'marker': 
                    selectMarkerBox.mapsmarkerType = 'marker';
                    break;
            }
        },
        getShortCode : function(){
          return '[mapsmarker '+ selectMarkerBox.mapsmarkerType +'="'+ selectMarkerBox.markerID +'"]';  
        },
        insert : function() {
            tinyMCEPopup.editor.execCommand('mceInsertContent', false, selectMarkerBox.getShortCode());
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
    
    selectMarkerBox.init();
})(jQuery)
</script>

</body>
</html>
<?php    
  exit;
}

function buildMarkersList($array){
?>    
    <?php foreach($array as $one):

		$date = DateTime::createFromFormat('Y-m-d H:i:s', $one['createdon']);

	?>
    <div class="list_item">
        <span class="name"><?php echo $one['name']?></span><span class="date"><?php echo $date->format('Y/m/d'); ?></span>
        <input type="hidden" value="<?php echo $one['type']?>" name="msb_type">
        <input type="hidden" value="<?php echo $one['id']?>" name="msb_id">
    </div>
    <?php endforeach; ?>  
<?php
}
?>
