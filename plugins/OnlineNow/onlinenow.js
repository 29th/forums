$(document).ready(function() {
	function GetOnline() {
		var url = gdn.url('/plugin/imonline');
		
		$.ajax({
			url: url,
			global: false,
			type: "GET",
			data: null,
			dataType: "html",
			success: function(Data){
				$("#OnlineNow").replaceWith(Data);
				setTimeout(GetOnline, gdn.definition('OnlineNowFrequency') * 100000);
			}
		});
	}

	GetOnline();
});


