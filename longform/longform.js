// functions to run on posts on page load
// or after we finish editing them
$.postProcessing = function(){

	$(".post-body .text img").not("[alt='']").each(function(){
		var alttext = $(this).attr("alt");
		var cite = $("<cite></cite>").addClass("aside left").text( alttext );
		$(this).parents("p").prepend(cite);
	});
}

$(document).ready(function(){
	$.postProcessing();
});
