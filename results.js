$(function () {
	// To expand lists in sidebar
	var list_dropdowns = document.getElementsByClassName('expand-dropdown');
	for (var i = 0; i < list_dropdowns.length; i++) {
		list_dropdowns[i].firstChild.addEventListener('click', expand_ul(list_dropdowns[i]));
	}
	function expand_ul(element) {
		return function () {
			var icon = element.getElementsByTagName('i')[0];
			if (icon.className === 'fas fa-angle-right') {
				icon.className = 'fas fa-angle-down';
				var li = element.getElementsByTagName('li');
				for (var i = 0; i < li.length; i++) {
					li[i].style.display = 'block';
				}
			}
			else {
				icon.className = 'fas fa-angle-right';
				var li = element.getElementsByTagName('li');
				for (var i = 0; i < li.length; i++) {
					li[i].style.display = 'none';
				}
			}
		}
	}
})