$(function() {
	$('input:radio', '.js-poll-options').click(function() {
		var $this = $(this);
		var poll_id = $this.data('poll-id');
		var option_id = $this.data('option-id');

		var $poll_wrap = $('.poll-' + poll_id);

		$poll_wrap.fadeOut(300);

		setTimeout(function() {
			$.get(intelli.config.url + 'polls.json', {id: option_id, poll_id: poll_id}, function(data) {
				$poll_wrap.html(data.results).fadeIn(300);
			});
		}, 300);
	});
});