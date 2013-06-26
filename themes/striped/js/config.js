/*
	Striped 2.0 by HTML5 Up!
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

window._skel_config = {
	prefix: 'themes/striped/css/style',
	resetCSS: true,
	useOrientation: true,
	breakpoints: {
		'mobile': {
			range: '-640',
			lockViewport: true,
			containers: 'fluid',
			grid: {
				collapse: true
			}
		},
		'desktop': {
			range: '641-',
			containers: 1200
		},
		'wide': {
			range: '1201-'
		},
		'narrow': {
			range: '641-1200',
			containers: 960
		},
		'narrower': {
			range: '641-1000'
		}
	}
};

window._skel_ui_config = {
	panels: {
		sidePanel: {
			breakpoints: 'mobile',
			position: 'left',
			style: 'reveal',
			size: '250px',
			html: '<div data-action="moveElement" data-target="sidebar"></div>'
		},
		sidePanelNarrower: {
			breakpoints: 'narrower',
			position: 'left',
			style: 'reveal',
			size: '300px',
			html: '<div data-action="moveElement" data-target="sidebar"></div>'
		}
	},
	bars: {
		titleBar: {
			breakpoints: 'mobile',
			position: 'top',
			size: 44,
			style: 'floating',
			html: '<div class="toggle " data-action="panelToggle" data-target="sidePanel"></div>' +
				  '<div class="title" data-action="copyHTML" data-target="logo"></div>'
		},
		titleBarNarrower: {
			breakpoints: 'narrower',
			position: 'top',
			size: 60,
			style: 'floating',
			html: '<div class="toggle " data-action="panelToggle" data-target="sidePanelNarrower"></div>' +
				  '<div class="title" data-action="copyHTML" data-target="logo"></div>'
		}
	}
};