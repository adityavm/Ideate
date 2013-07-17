function url_pathinfo(str){
	var m = str.match(/http:\/\/i.imgur.com\/(\w+)\.(png|jpg|jpeg|gif)/);
	return m;
}

$(document).ready(function(){
	$(".new-idea").on("click", function(){
		$ideas = $("<div class='top-cont'></div>");
		$contr = $("<div class='bot-cont'></div>");
		$container = $("<div class='ideas-selector'></div>");
		$wrap = $("<div class='ideas-selector-wrap'></div>");

		$ideas.append(
			$("<table></table>").append(
				$("<tr></tr>").append(
					$("<td></td>").append(
						$("<input type='text' class='idea-title' placeholder='Briefly Describe the Idea'></input>")
					),
					$("<td></td>")
				),
				$("<tr></tr>").append(
					$("<td></td>").append(
						$("<textarea class='idea-desc' placeholder='Slightly longer description'></textarea>")
					),
					$("<td></td>")
				),
				$("<tr></tr>").append(
					$("<td></td>").append(
						$("<input type='text' class='idea-bg-img' placeholder='Background Image (optional)'></input>").on("blur", function(){
							var url = url_pathinfo($(this).val());
							$(".idea-img").css("background-image", "url('http://imgur.com/"+ url[1] +"s."+ url[2] +"')");
						})
					),
					$("<td></td>").append(
						$("<div class='idea-img preview'></div>")
					)
				),
				$("<tr></tr>").append(
					$("<td></td>").append(
						$("<input type='text' class='idea-bg-color' placeholder='Accent Colour'></input>").on("blur", function(){
							var color = $(this).val();
							$(".idea-color").css("background-color", color);
						})
					),
					$("<td></td>").append(
						$("<div class='idea-color preview'></div>")
					)
				)
			)
		)

		$contr.append(
			$("<div class='btn save'>Create</div>").on("click", function(){
				var title = $(".top-cont .idea-title").val();
				var desc = $(".top-cont .idea-desc").val();
				var img	  = $(".top-cont .idea-bg-img").val();
				var color = $(".top-cont .idea-bg-color").val();
				$.post("/tb/all/post.idea.php", {
					title:	title,
					desc:	desc,
					image:	img,
					color:	color
				}, function(data){
					if(data.iid){
						$(".idea.new-idea").before(
							$("<div class='idea square'></div>").append(
								$("<a href='../idea/'"+ data.pid +"></a>").append(
									$("<div class='idea-circle'></div>").css({
										"background-image": "url('"+ img +"')",
										"background-color": color
									}),
									$("<div class='idea-title'>"+ title +"</div>")
								)
							)
						)
					}

					$wrap.fadeOut("fast", function(){ $wrap.remove(); });
				});
			}),
			$("<div class='btn'>Cancel</div>").on("click", function(){
				$wrap.fadeOut("fast", function(){ $wrap.remove(); });
			})
		)
	
		$container.append($ideas, $contr);
		$wrap.append($container);
		$("body").append($wrap);
	});
});
