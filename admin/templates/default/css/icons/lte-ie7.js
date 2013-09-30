/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'icomoon\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-publish' : '&#xe000;',
			'icon-cog' : '&#xe001;',
			'icon-power-cord' : '&#xe002;',
			'icon-folder' : '&#xe003;',
			'icon-coffee' : '&#xf0f4;',
			'icon-comment-alt' : '&#xf0e5;',
			'icon-bell' : '&#xe004;',
			'icon-picture' : '&#xf03e;',
			'icon-gauge' : '&#xe005;',
			'icon-signout' : '&#xf08b;',
			'icon-house' : '&#xe006;',
			'icon-unlock-alt' : '&#xf13e;',
			'icon-sad' : '&#xe007;',
			'icon-code' : '&#xe008;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};