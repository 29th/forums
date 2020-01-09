jQuery(function($) {	
	$('.InboxClearConversation').popup({
		confirm: true,
		followConfirm: false,
		afterConfirm: function(json, sender) {
			json.RedirectUrl = false;
			var row = $(sender).parents('li:first');
			$(row).slideUp('fast', function() {
				$(row).remove();
			});
		}
	});
});
