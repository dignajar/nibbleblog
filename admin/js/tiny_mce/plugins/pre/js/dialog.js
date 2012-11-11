tinyMCEPopup.requireLangPack();

var PreDialog = {
	init : function(ed, url) {
		this.resize();
		var cnt = (ed.selection.getNode().nodeName == 'CODE') ? ed.selection.getNode().innerHTML : '';		
		document.getElementById('content').value = tinyMCEPopup.dom.decode(cnt);
	},

	insert : function() {	
		var cnt = tinyMCEPopup.dom.encode(document.getElementById('content').value);
		var ed = tinyMCEPopup.editor;
		if(ed.selection.getNode().nodeName == 'CODE') {
			ed.selection.getNode().innerHTML = cnt;
		}
		else {
			cnt = '<pre><code>'+cnt+'</code></pre><p></p>';
			tinyMCEPopup.execCommand(tinyMCEPopup.isGecko ? 'insertHTML' : 'mceInsertContent', false, cnt);
		}
		
		tinyMCEPopup.restoreSelection();
		tinyMCEPopup.close();
	},
	
	resize : function() {
		var vp = tinyMCEPopup.dom.getViewPort(window), el;

		el = document.getElementById('content');

		el.style.width  = (vp.w - 20) + 'px';
		el.style.height = (vp.h - 50) + 'px';
	}
};

tinyMCEPopup.onInit.add(PreDialog.init, PreDialog);
