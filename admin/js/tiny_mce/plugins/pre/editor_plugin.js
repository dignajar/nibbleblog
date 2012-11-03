(function() {
	tinymce.PluginManager.requireLangPack('pre');

	tinymce.create('tinymce.plugins.PrePlugin', {		
		init : function(ed, url) {			
			var t = this;
			t.editor = ed;
			
			ed.addCommand('mcePre', function() {
				ed.windowManager.open({
					file : url + '/dialog.htm',
					width : 500 + parseInt(ed.getLang('pre.delta_width', 0)),
					height : 300 + parseInt(ed.getLang('pre.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			ed.addButton('pre', {
				title : 'pre.desc',
				cmd : 'mcePre',
				image : url + '/img/pre.gif'
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('pre', n.nodeName == 'CODE');
				
				if(n.nodeName == 'CODE') t._setDisabled(1);
				else t._setDisabled(0); 
			});
		},
		
		createControl : function(n, cm) {
			return null;
		},

		getInfo : function() {
			return {
				longname : 'Pre plugin',
				author : 'Marchenko Alexandr',
				authorurl : 'http://webdiz.com.ua',
				infourl : 'http://webdiz.com.ua',
				version : "1.0"
			};
		},
		
		
		_block : function(ed, e) {
			var k = e.keyCode;

			// Don't block arrow keys, pg up/down, and F1-F12
			if (k == 46 || (k > 32 && k < 41) || (k > 111 && k < 124))
				return;

			if (e.preventDefault) {
				e.preventDefault();
				e.stopPropagation();
			}
			else {
				e.returnValue = false;
				e.cancelBubble = true;
			}
			return false;
		},

		_setDisabled : function(s) {
			var t = this, ed = t.editor;

			tinymce.each(ed.controlManager.controls, function(c) {
				if(c.settings.cmd != 'mcePre') {
					c.setDisabled(s);
				}
			});

			if (s !== t.disabled) {
				if (s) {
					ed.onKeyDown.addToTop(t._block);
					ed.onKeyPress.addToTop(t._block);
					ed.onKeyUp.addToTop(t._block);
					ed.onPaste.addToTop(t._block);
				} else {
					ed.onKeyDown.remove(t._block);
					ed.onKeyPress.remove(t._block);
					ed.onKeyUp.remove(t._block);
					ed.onPaste.remove(t._block);
				}

				t.disabled = s;
			}
		}
	});

	// Register plugin
	tinymce.PluginManager.add('pre', tinymce.plugins.PrePlugin);
})();
