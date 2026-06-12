// Basic client-side validation for auth and post forms
(function(){
	document.addEventListener('submit', function(e){
		var form = e.target;
		if (form.matches('form')) {
			var pw = form.querySelector('input[type=password]');
			if (pw && pw.value.length > 0 && pw.value.length < 6) {
				alert('Password must be at least 6 characters');
				e.preventDefault();
			}
		}
	});
})();

