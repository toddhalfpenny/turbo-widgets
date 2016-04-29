(function() {
    tinymce.create('tinymce.plugins.TurboPlugin', {
        init : function(ed, url) {
            var t = this;
            ed.addCommand('turboShortCode', function() {
            tb_show( 'Add a new Turbo Widget', 'admin-ajax.php?action=turbo_editor_dialog' );
            });
            ed.addButton('turboplugin', {
                title : 'Add a widget',
                  cmd : 'turboShortCode'
            });
            //replace shortcode before editor content set
            ed.onBeforeSetContent.add(function(ed, o) {
                o.content = t._do_turbo(o.content);
                //console.log('onBeforeSetContent -> '+ o);
            });

            //replace shortcode as its inserted into editor (which uses the exec command)
            ed.onExecCommand.add(function(ed, cmd) {
                if (cmd ==='mceInsertContent'){
                    tinyMCE.activeEditor.setContent( t._do_turbo(tinyMCE.activeEditor.getContent()) );
                }
                //console.log('onExecCommand -> '+ cmd);
            });
            //replace the image back to shortcode on save
            ed.onPostProcess.add(function(ed, o) {
                if (o.get)
                    o.content = t._get_turbo(o.content);
                //console.log('onPostProcess -> '+ o);
            });


            ed.on( 'click', function( e ) {
                if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('turbo_widget') > -1 ) {;
                    console.log(e);
                    return false;
                }
            } );

             ed.on( 'wptoolbar', function( event ) {
                // hide the toolbar that pops up for WordPress images
                if (event.element.className.includes("turbo_widget") && event.toolbar) {
                    window.setTimeout( function() {
                        event.toolbar.hide();
                    }, 0 );
                    return false;
                }
            } );
        },
        _do_turbo : function(co) {
            return co.replace(/\[turbo_widget([^\]]*)\]/g, function(a,b){
                // Could this commented code to have a specific img.
                b = b.replace(/&amp;widget-/g, '&widget-');
                b = b.replace(/&amp;obj-class=/g, '&obj-class=');
                var queryDict = {};
                b.substr(1).split("&").forEach(function(item) {queryDict[item.split("=")[0]] = item.split("=")[1]});
                return '<img src="../wp-content/plugins/turbo-widgets/images/tw.png" class="turbo_widget mceItem" data-sh-attr="turbo_widget'+tinymce.DOM.encode(b)+'" title="Turbo Widget: ' + queryDict['widget-prefix'] + ' " />';

                //return '<a class="turbo_widget mceItem" data-sh-attr="turbo_widget" >Edit me</a>';
            });
        },

        _get_turbo : function(co) {

            function getAttr(s, n) {
                n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
                return n ? tinymce.DOM.decode(n[1]) : '';
            };

            return co.replace(/(?:<p[^>]*><a[^>]*>)*(<img[^>]+>)(?:<\/a><\/p>)*/g, function(a,im) {
                var cls = getAttr(im, 'class');

                if ( cls.indexOf('turbo_widget') != -1 )
                    return '<p>['+tinymce.trim(getAttr(im, 'data-sh-attr'))+']</p>';

                return a;
            });
        },

        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Turbo Widgets",
                author : 'Todd Halfpenny',
                authorurl : 'http://gingerbreaddesign.co.uk/todd/',
                infourl : 'http://gingerbreaddesign.co.uk/wordpress/turbo-widgets/',
                version : "0.0.1"
            };
        }
    });
    tinymce.PluginManager.add('turboplugin', tinymce.plugins.TurboPlugin);
})();

function DisplayFormValues()
{
    var value = jQuery("#current-widget").val();
    var theDiv = jQuery(value);
    childElems = theDiv.children();
    var data = {};
    theDiv.find('input, textarea, select').each(function(i, field) {
        //var item = {field.name : field.value};
        //data.push(item);
        var type = jQuery(this).prop('type');
        if (type == "checkbox" || type=="radio"){
            data[field.id] = jQuery(this).prop('checked');
        } else {
            data[field.id] = jQuery.trim(field.value);
        }
    });
    var str = jQuery.param(data)
    return str;
}

function insertShortCode(updateExisting){
    var args = DisplayFormValues();
    console.log('args', args);
    if (updateExisting == 1){
        tinyMCE.activeEditor.selection.setContent('');
    }
    tinyMCE.activeEditor.execCommand( "mceInsertContent", false, '[turbo_widget ' + args + ']' );
        tb_remove();
}