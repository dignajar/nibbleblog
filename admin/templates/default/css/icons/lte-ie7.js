/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'icomoon\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-publish' : '&#xe002;',
			'icon-cog' : '&#xe003;',
			'icon-power-cord' : '&#xe004;',
			'icon-folder' : '&#xe001;',
			'icon-coffee' : '&#xf0f4;',
			'icon-image' : '&#xe006;',
			'icon-gauge' : '&#xe000;',
			'icon-signout' : '&#xf08b;',
			'icon-comment-alt' : '&#xf0e5;',
			'icon-user' : '&#xe005;',
			'icon-bell' : '&#xe007;',
			'icon-sad' : '&#xe008;',
			'icon-code' : '&#xe009;',
			'icon-house' : '&#xe00a;'
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