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

var post;

$(document).ready(function(){
	var $edit = $(".edit-post");
	var $save = $(".save-post");
	var $cancel = $(".cancel-post");
	var $editcover = $(".edit-cover-btn");
	var $covertext = $(".post-cover-text");
	var $title = $(".post-title");
	var $titlewrap = $(".post-title-wrap");

	$save.on("click", function(){
		var pid = $(this).data("pid");
		var editor = $(".post").data("cm-ed");
		var converter = new Showdown.converter();

		if(editor){// active form
			$.post("/tb/longform/post.longform.php", {
				id: (pid == 0) ? null : pid,
				title: $(".post-title").text(),
				text: editor.getValue(),
				cover: $(".post-title-wrap").data("cover"),
				covertxt: $(".post-cover-text").text()
			}, function(ret){
				$title.html( ret.title );
				$(".post-body .text").html( ret.html );

				$edit.show();
				$save.hide();
				$cancel.hide();
				$editcover.hide();
				$covertext.removeAttr("contenteditable");
				if(ret.cover)
					$covertext.addClass("hide");
				else
					$covertext.html(ret.covertxt);
				$title.removeAttr("contenteditable");
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
	$edit.on("click", function(data){
		var id = $(this).data("pid");
		var converter = new Showdown.converter();
		$(this).addClass("working").html("&hellip;");

		$.getJSON("/tb/longform/get.longform.php", {id: id}, function(ret){
			post = ret;
			$(".body").addClass("editing");
			$edit.hide().removeClass("working").html("Edit");// reset edit button
			$save.show();
			$cancel.show();
			$editcover.show();
			$covertext.attr("contenteditable", "true").removeClass("hide").text(ret.covertxt);

			// make title editable
			$title.attr("contenteditable", "true");

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
	$cancel.on("click", function(){
		var $this = $(this);

		// confirm cancel
		if(!$this.hasClass("confirm")){
			$(this).addClass("confirm");
			return;
		}

		var converter = new Showdown.converter();

		$covertext.html( converter.makeHtml(post.covertxt) );//cover text
		$(".post-body .text").html( converter.makeHtml(post.raw) );//post body
		$.postProcessing();
		$(".post-title").text( post.title );//post title
		$titlewrap.data("cover", post.cover).css("background-image", ((post.cover)?"url('"+post.cover+"')":"none"));//cover photo
		if(post.cover) $titlewrap.addClass("post-cover"); else $titlewrap.removeClass("post-cover");
		$(".form").fadeOut("fast", function(){//editor
			$(".form").remove();
			$(".body").removeClass("editing");
		});
		$(".post").data("cm-ed", null);
		$editcover.hide();//cover edit btn
		$cancel.removeClass("confirm").hide();//cancel btn
		$save.hide();//save btn
		$edit.show();//edit btn
	});
	$editcover.on("click", function(){
		var url = $titlewrap.data("cover");
		var $editorwrap;
		$titlewrap.append(
			$editorwrap = $("<div class='post-cover-editor'></div>").append(
				$("<input type='text'></input>").val(url).on("keydown", function(e){
					var $this = $(this); 
					url = $(this).val();
					if(e.keyCode == 13){//enter
						if($.trim(url) != ""){
							$titlewrap.data("cover", url).css("background-image", "url('"+ url +"')").addClass("post-cover");
						} else {
							$titlewrap.data("cover", null).css("background-image", "none").removeClass("post-cover");
						}
						$editorwrap.fadeOut("fast", function(){ $editorwrap.remove(); });
					}
					if(e.keyCode == 27){//esc
						$editorwrap.fadeOut("fast", function(){ $editorwrap.remove(); });
					}
				})
			)
		);
	});
});
