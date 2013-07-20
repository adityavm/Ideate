function editButtonHandler(){
	var $this = $(this).parents("div.post");
	var pid = $(this).data("pid");
	var $form;

	$form = postForm(pid);
	$this.children(".post-body").add($this.children(".edit-btns")).add($this.children(".related-to")).fadeOut("fast", function(){
		$this.append($form.fadeIn("fast"));
		$form.find("textarea").data("cm-ed").focus();
	});
}

function newPostButtonHandler(){
	//create new post form
	$p = $(".new-post").parents("p").hide();
	$form = postForm();

	$(".body").append($form.fadeIn("fast"));
	$form.find("textarea").data("cm-ed").focus();
}

function postForm(id){

	var data;

	var $form = $("<div class='new-post-form'></div>");
	var $cont = $("<div class='edit-btns'></div>");
	var $text = $("<textarea class='edit-post-text'></textarea>").attr("placeholder", "So, what's new in \""+ idea.title +"\" today?");
	var $link = $("<input class='edit-post-link' type='text' placeholder='/via'></input>");

	if (id)
		$.getJSON("/tb/idea/get.post.php", {id: id}, function(ret){
			data = ret;
			$text.data("cm-ed").setOption("value", data.raw);
			$link.val(data.link);
		});

	// tag related ideas to this post
	var $tag = $("<a class='icon-tag'></a>");
		$tag.append( $("<span>Tag other ideas</span>") );
		$tag.on("click", function(){
			var tagged = [];

			(function(tagged){
				$.getJSON("/tb/idea/get.ideas.php", function(data){
					$ideas = $("<div class='top-cont'></div>");
					$contr = $("<div class='bot-cont'></div>");
					$container = $("<div class='ideas-selector'></div>");
					$wrap = $("<div class='ideas-selector-wrap'></div>");

					$.getJSON("/tb/idea/get.relations.php", {pid: id}, function(rels){
						for(var i=0;i<data.length;i++){
							var d = data[i];
							if(d.iid == idea.id)
								continue;

							$ideas.append(
								$("<div class='idea'></div>").append(
									$("<div class='idea-circle'></div>").css({ 'background-image': 'url("'+d.bg_img_small+'")', 'background-color': d.bg_color }),
									$("<div class='idea-title'></div>").text( d.title )
								).data("id", d.iid)
								.on("click", function(){
									var $this = $(this);
									var id = $this.data("id");
									if(tagged.indexOf(id) > -1){
										tagged.splice( tagged.indexOf(id), 1 );
										$this.removeClass("tag-on");
									} else {
										tagged.push(id);
										$this.addClass("tag-on");
									}

									console.log("Tagged: "+ tagged);
								})
							)

							if(rels.indexOf(d.iid) > -1){
								$ideas.find(".idea").addClass("tag-on");
								tagged.push(d.iid);
							}
						}

						$contr.append(
							$("<div class='btn save'>Tag</div>").on("click", function(){
								$.post("/tb/idea/post.relations.php", {pid: id, iid: idea.id, iids: tagged}, function(data){
									// draw related ideas as branch
									$wrap.fadeOut("fast", function(){ $wrap.remove(); });
								});
							}),
							$("<div class='btn'>Cancel</div>").on("click", function(){
								$wrap.fadeOut("fast", function(){ $wrap.remove(); });
							})
						);

						$container.append($ideas, $contr);
						$wrap.append($container);
						$("body").append($wrap);
					});
				});
			})(tagged);
		});
	// save this post
	var $save = $("<a class='icon-ok'></a>");
		$save.append( $("<span>Save post</span>") );
		$save.on("click", function(){
			$.post("/tb/idea/post.post.php", {
				id: id,
				iid: idea.id,
				title: "",
				text: $.trim($text.data("cm-ed").getValue()),
				link: $link.val()
			}, function(ret){
				if(id){ //existing post
					$form.fadeOut("fast", function(){
						$("#"+id+" .post-body .text").html(ret.html)

						$("#"+id+" .post-body").fadeIn("fast");
						$("#"+id+" .edit-btns").show();
						$("#"+id+" .related-to").show();
						$form.remove();
					});
				} else {
					$form.fadeOut("fast", function(){ $form.remove(); });
					$("p.sep").before(
						$("<div class='post' id='#"+ ret.pid +"'></div>").append(
							$("<div class='edit-btns'></div>").append(
								$("<a class='edit-post icon-pencil' data-pid='"+ ret.pid +"'></a>")
								 .append( $("<span>Edit post</span>") )
								 .on("click", editButtonHandler)
							),
							$("<div class='post-body'></div>").append(
								$("<a class='upd' href='#"+ ret.pid +"'>"+ ret.created_substr +"</a>"),
								$("<div class='text'>"+ ret.html +"</div></div>")
							)
						)
					)
					$("p.sep").fadeIn("fast");
				}
			}, 'json');
		});
	// cancel all edits to this post
	var $canc = $("<a class='icon-remove'></a>");
		$canc.append( $("<span>Discard changes</span>") );
		$canc.on("click", function(){
			$form.fadeOut("fast", function(){
				$("#"+id+" .post-body").show();
				$("#"+id+" .edit-btns").show();
				$("#"+id+" .related-to").show();
				$form.remove();
				$("p.sep").fadeIn("fast");
			});
		});
	$cont.append($tag, $save, $canc);

	$form.append($cont, $text, $link);
	var editor = CodeMirror.fromTextArea($text.get(0), {
        mode: "markdown",
        theme: "elegant",
		lineWrapping: "true",
		viewportMargin: Infinity,
        extraKeys: {"Enter": "newlineAndIndentContinueMarkdownList"}
	});
	$text.data("cm-ed", editor);
	$form.hide();
	return $form;
}

$(document).ready(function(){
	$(".edit-post").on("click", editButtonHandler);
	$(".new-post").on("click", newPostButtonHandler);
});
