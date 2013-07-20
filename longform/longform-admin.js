function newPost(){
	var converter = new Showdown.converter();

	$(".body").addClass("editing");
	$(".edit-post").hide().removeClass("working").html("Edit");// reset edit button
	$(".save-post").show();

	// make title editable
	$(".post-title").attr("contenteditable", "true");

	// post editor
	var $form = $("<div class='form hide'></div>");
	$(".post-body").before($form);

	var editor = CodeMirror($form.get(0), {
		mode: "markdown",
		theme: "elegant",
		lineWrapping: "true",
		viewportMargin: Infinity,
		extraKeys: {"Enter": "newlineAndIndentContinueMarkdownList"}
	});
	editor.on("change", function(){
		// is this too heavy?
		$(".post-body .text").html( converter.makeHtml( editor.getValue() ) );
	});

	$(".post").data("cm-ed", editor);

	$form.removeClass("hide");
	editor.focus();
}

$(document).ready(function(){
	$(".save-post").on("click", function(){
		var pid = $(this).data("pid");
		var editor = $(".post").data("cm-ed");

		if(editor){// active form
			$.post("/tb/idea/post.post.php", {
				id: (pid == 0) ? null : pid,
				iid: 0,
				title: $(".post-title").text(),
				text: editor.getValue(),
				link: ""
			}, function(ret){
				$(".post-title").html( ret.title );
				$(".post-body .text").html( ret.html );

				$(".edit-post").show();
				$(".save-post").hide();
				$(".post-title").removeAttr("contenteditable");
				$(".form").fadeOut("fast", function(){ 
					$(".form").remove();
					$(".body").removeClass("editing");
				});
				$(".post").data("cm-ed", null);

				$.postProcessing();

				if(pid == 0)
					window.location = "/longform/"+ret.pid
			}, 'json')
		}
	});
	$(".edit-post").on("click", function(data){
		var id = $(this).data("pid");
		var converter = new Showdown.converter();
		$(this).addClass("working").html("&hellip;");
		$.getJSON("/tb/idea/get.post.php", {id: id}, function(ret){
			$(".body").addClass("editing");
			$(".edit-post").hide().removeClass("working").html("Edit");// reset edit button
			$(".save-post").show();

			// make title editable
			$(".post-title").attr("contenteditable", "true");

			// post editor
			var $form = $("<div class='form hide'></div>");
			$(".post-body").before($form);

			var editor = CodeMirror($form.get(0), {
				value: ret.raw,
				mode: "markdown",
				theme: "elegant",
				lineWrapping: "true",
				viewportMargin: Infinity,
				extraKeys: {"Enter": "newlineAndIndentContinueMarkdownList"}
			});
			editor.on("change", function(){
				// is this too heavy?
				$(".post-body .text").html( converter.makeHtml( editor.getValue() ) );
			});

			$(".post").data("cm-ed", editor);

			$form.removeClass("hide");
			editor.focus();
		});
	});
});
