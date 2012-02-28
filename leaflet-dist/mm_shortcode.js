(function($) {
    tinymce.create('tinymce.plugins.mm_shortcode', {
        init : function(ed, url) {
			ed.addCommand('mm_shortcode', function() {
                                ed.windowManager.open({
                                        title : 'Insert/Edit link',
					file : '/wp-admin/admin-ajax.php?action=get_mm_list',
					width : 450 + parseInt(ed.getLang('example.delta_width', 0)),
					height : 450 + parseInt(ed.getLang('example.delta_height', 0)),
                                        inline: 1
				})
			});
                        
			ed.addButton('mm_shortcode', {title : 'Add maps marker shortcode', cmd : 'mm_shortcode', image: url+'/../img/icon-menu-page.png' });
        },
        createControl : function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('mm_shortcode', tinymce.plugins.mm_shortcode);
})(jQuery);

