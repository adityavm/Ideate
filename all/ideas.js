function drawHome(){
	var innerW = $(window).get(0).innerWidth;
	var innerH = $(window).get(0).innerHeight;
	var paper = Raphael($(".body").get(0),innerW,innerH-60);
	var origin = {x:innerW/2, y:innerH/2-30};

	var n = ideas.length;
	var r = 250;
	var rad = 45;//(Math.PI*r - 80*n)/n;
	var ang = 0;
	var coords = {};

	console.group("Load "+ (new Date()).getTime());

	// generate coordinates for each idea circle and store
	// in object for future referencing (for links and circles)
	for(var i=0;i<n;i++){
		var idea = ideas[i];

		pir = ang * Math.PI/180;
		coords[idea.iid] = {x:origin.x - r*Math.sin(pir),y:origin.y - r*Math.cos(pir)};
		ang += 360/n;
	}

	console.group("Coordinates");
	console.log(coords);
	console.groupEnd();

	// make paths to show relations before circles because svg
	// draws later shapes over earlier shapes
	for(var i=0;i<n;i++){
		var idea = ideas[i];

		var p; // for path
		if(links[idea.iid])
			$.each(links[idea.iid], function(i,v){
				// i is dest, v is weight
				console.group("Links for "+ idea.iid);
				console.log(i,v);
				
				var l = i;
				
				var src_c = coords[idea.iid];
				var dst_c = coords[l];
				console.log(src_c, dst_c);
				
				p = paper.path("M"+src_c.x+","+src_c.y+"L"+dst_c.x+","+dst_c.y);
				p.attr("stroke","#888");
				p.attr("stroke-width", Math.min(v,4));
				p.attr("stroke-dasharray","- ");
				
				console.groupEnd();
			});
	}

	// draw idea circles
	console.group("Ideas");
	for(var i=0;i<n;i++){
		var idea = ideas[i];
		console.log(idea);

		var c = paper.circle(coords[idea.iid].x, coords[idea.iid].y, rad)
			c.attr("stroke", "rgba(0,0,0,0.5)");
			c.attr("fill", idea.bg_color);

		if(idea.bg_img)
			c.attr("fill", "url('"+ idea.bg_img_small +"')");
			
			(function(id){
			c.click(function(){
				top.window.location = "../idea/"+id;
			});
			})(idea.iid)

		var t = paper.text(coords[idea.iid].x, (coords[idea.iid].y+rad+15), idea.title);
			t.attr("fill", "#333");
			t.attr("font", "400 10pt 'proxima-nova', sans-serif");
			t.click(function(){
				top.window.location = "../idea/"+idea.iid;
			});
	}
	console.groupEnd();

	console.groupEnd();
}

$(document).ready(function(){
	$(".body").css({
		"height": (window.innerHeight - 60) + "px",
		"width":  (window.innerWidth) + "px"
	});
	drawHome();
});
