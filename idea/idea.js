function doFootnotes(){
	$(".footnotes li").each(function(){
		var fnLink = $(this).find("a[rev=footnote]");
		var relTo = $(this).parents(".post").find(".related-to .fr");

		var href = fnLink.attr("href");
			href = href.replace(/\:/g, "\\:");
		var supStr = "sup"+href;
		var fnLoc = $(this).parents(".text").find(supStr);
			fnLocText = fnLoc.text();

		fnLink.remove();
		var fnText = $( "<div>"+ $(this).html() +"</div>").find("p:eq(0)").prepend( fnLocText+". " )

		console.log(fnText);

		relTo.before(
			$("<div class='fn'></div>")
				.html( fnText )
				//.css("top", fnLoc.position().top+"px")
		)
	});
	$("a[rel=footnote]").each(function(){
		var fn = $(this).text();
		$(this).before(fn);
		$(this).remove();
	});
}

$(document).ready(function(){
	doFootnotes();
});
