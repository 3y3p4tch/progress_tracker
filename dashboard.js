$(function () {
	// For configuring Latex
	MathJax.Hub.Config({
		tex2jax: {
			inlineMath: [['$', '$'], ['\\(', '\\)']], // for allowing use of $
			processEscapes: true, // for interpreting \$ as literal $
			preview: '[math]', // text to display while processing
			skipTags: ["script", "noscript", "style", "pre", "code"], //for ignoring these tags
			processClass: 'latex' //for processing textarea if it contains class latex
		}
		// Use MathJax.Hub.Typeset() to re-run typesetting
	});

	// For preview of the code above.
	$("#details").bind('keyup', function (e) {
		// console.log(e.which);
		var str = $(this).val();
		$("#preview_latex").text(str);
		if ($("#preview_latex").css('display') != 'none')
			MathJax.Hub.Typeset();
	});

	// for textarea resize
	var textarea = document.getElementById('details');
	function resize() {
		console.log('hi');
		textarea.style.height = 'auto';
		textarea.style.height = textarea.scrollHeight + 'px';
	}
	function delayedResize() {
		setTimeout(resize, 0);
	}
	textarea.addEventListener('cut', delayedResize);
	textarea.addEventListener('keydown', delayedResize);
	textarea.addEventListener('drop', delayedResize);
	textarea.addEventListener('paste', delayedResize);

})