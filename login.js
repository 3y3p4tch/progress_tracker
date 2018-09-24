$(function () {
	// for animation in in side-bar
	particlesJS.load('init_info', 'assets/particles.json');

	// to set focus initially on ldap
	$('#ldap').focus();
	window.history.pushState(null, 'Invalid Credentials', '/dashboard.php');
});
