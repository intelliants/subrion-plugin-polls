$(document).ready(function()
{
	$("ul.poll-options li input:radio").click(function()
	{
		var option_id;
		option_id = $(this).attr("id");
		option_id = option_id.split("-");
		
		// extract option id
		var  poll_id = option_id[2];
		option_id = option_id[3]; 
		
		$("#poll-" + poll_id).fadeOut(300);
		
		setTimeout(function() {
			$.get(intelli.config.ia_url + "poll/vote.json", {id: option_id, poll_id: poll_id}, function(data)
			{
				$("#poll-" + poll_id).html(data.results).fadeIn(300);
			});
		}, 300);
	});
});