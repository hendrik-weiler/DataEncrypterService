window.onload = function() {
	var branches = document.getElementsByClassName('folder'),
		i = 0,
		len = branches.length;

	for (i ; i < len; ++i) {
		branches[i].onclick = function() {
			var ul = this.parentNode.getElementsByTagName('ul')[0];
			if(ul.getAttribute('data-closed') == 'false') {
				ul.style.display = null;
				ul.setAttribute('data-closed','true');
			} else {
				ul.style.display = 'none';
				ul.setAttribute('data-closed','false');
			}
			ul.style.listStyle = "square";
			setTimeout(function() {
				ul.style.listStyle = 'none';
			},1000);
		}
		branches[i].onclick();
	};
	branches[0].onclick();
}