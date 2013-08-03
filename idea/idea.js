function doFootnotes(){
	$(".footnotes li").each(function(){
		var fnLink = $(this).find("a[rev=footnote]"),
			relTo = $(this).parents(".post").find(".related-to"),
			fns = $(this).parents(".post").find(".fns");

		var href = fnLink.attr("href");
			href = href.replace(/\:/g, "\\:");
		var supStr = "sup"+href;
		var fnLoc = $(this).parents(".text").find(supStr);
			fnLocText = fnLoc.text();
			fnLoc.addClass("fn-link");

		fnLink.remove();
		var fnText = $( "<div>"+ $(this).html() +"</div>").find("p:eq(0)").prepend( $("<span class='fnNo'>"+fnLocText+"</span>") )

		var pid = $(this).parents(".post").attr("id");
		fns.append(
			$("<div class='fn' id='fnref-"+ pid +"-"+ fnLocText +"'></div>")
				.html( fnText )
		)

		fnLoc.data("fn", "#fnref-"+pid+"-"+fnLocText);
	});
	$("a[rel=footnote]").each(function(){
		var $this = $(this);
		var fn = $this.text();
		$this.before(fn);
		$this.remove();
	});
	$("sup.fn-link").bind("click.fn", function(e){
		var $this = $(this),
			fn = $( $this.data("fn") );

		$(".fn").not(fn).removeClass("show");
		fn.toggleClass("show");
	});
}

$(window).load(function(){
	$("sup.fn-link").each(function(){
		$( $(this).data("fn") ).css("top", ($(this).position().top+2)+"px")
	});
});

$(document).ready(function(){
	doFootnotes();
});
