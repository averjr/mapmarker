(function($) {
    function selectMarkerBox(ed){
        this.editor = ed;
        self = this;
    }
    selectMarkerBox.prototype = {
        init : function(){
            $('.map_marker').live('click', function(){
                console.log( $(this).text() );
            })

            $('#insertMarkerSC').live('click', function(e){
                e.preventDefault();
                self.insert();
            })
        },        
        setMarkerID : function(id) {
            this.markerID = id;
        },
        
        insert : function() {
            this.editor.selection.setContent('[mapsmarker marker="'+ this.markerID +'"]');
            tb_remove();
        }
    }

    
    tinymce.create('tinymce.plugins.mm_shortcode', {
        init : function(ed, url) {
			ed.addCommand('mm_shortcode', function() {
                                tb_show('', 'admin-ajax.php?action=get_my_form');
                                var markerBox = new selectMarkerBox(ed);
                                markerBox.init();
			});
                        
			ed.addButton('mm_shortcode', {title : 'Add maps marker shortcode', cmd : 'mm_shortcode', image: url+'/../img/icon-menu-page.png' });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('mm_shortcode', tinymce.plugins.mm_shortcode);
})(jQuery);

