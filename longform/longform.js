// ligaturise everything (kinda)
$.fn.ligature = function(str, lig) {
    return this.each(function() {
        this.innerHTML = this.innerHTML.replace(new RegExp(str, 'g'), lig);
    });
};

// functions to run on posts on page load
// or after we finish editing them
$.postProcessing = function(){

	$(".post-body .text img[alt]").each(function(){
		var alttext = $(this).attr("alt");
		var cite = $("<cite></cite>").addClass("aside left").text( alttext );
		$(this).parents("p").prepend(cite);
	});
}

$(document).ready(function(){
	$.postProcessing();
});
