// JavaScript Document


/* Ajax tab script */

$(document).ready(function(){
  $(".load").live("click",function(){
$(".loadpage").load("ajax/"+$(this).attr('rel'));
$(".booking-nav li").removeClass("active");
$(this).parent("li").addClass("active");
});

});
$(document).ready(function(){
  $(".load1").live("click",function(){
$(".loadpage1").load("ajax/"+$(this).attr('rel'));
$(".right-menu li").removeClass("bg-active");
$(this).parent("li").addClass("bg-active");
});

});

/* tooltip script */
$(document).ready(function(){

	$(".show-tip").live("click",function(event){
		var id=$(this).attr("id");
		var visi=$("#"+id+"-tip").is(":visible");
		$(".alltip").hide();
		if(visi){
				$("#"+id+"-tip").hide(300);
		}else{
			$("#"+id+"-tip").slideDown(300);

		}
		event.stopPropagation();

	});
	$(".alltip").children().click(function(event){
		event.stopPropagation();
	});
	$("html").live("click",function(ev){
		//alert(ev.target.attr('class'));
		$(".alltip").hide();
	});
});
/* message notificatoin pressed */

/* more thread notificaiton pressed page */

$(document).ready(function(){

  $(".more-btn1").live("click",function(){
setTimeout(function(){$("#more").load("ajax/more.html");},300);

  });

});

/* info tooltip */

$(document).ready(function(){

$(".show-info").live("mouseover",function(event){
		var id=$(this).attr("id");
		var visi=$("#"+id+"-tip").is(":visible");
		if(!visi){
			$("#"+id+"-tip").slideDown(0);

		}
		event.stopPropagation();

	});
   $(".info-pop").live("mouseover",function(){
		$(".info-pop").is("visible");
	});

	$("html").live("mouseover",function(){
		$(".info-pop").slideUp(0);
	});
});


/* pager script */

$(document).ready(function(){
var currentpage=1;
var maxpage=4;
 $(".load").click(function(){
 currentpage=eval($(this).text());
if(currentpage>=maxpage)
{
$(".next").parent("li").addClass("disabled");
}
else
{
$(".next").parent("li").removeClass("disabled");
}
	if(currentpage>1)
	{
	$(".prev").parent("li").removeClass("disabled");
	}
	else
	{$(".prev").parent("li").addClass("disabled");
	}
    	$(".paging").load("ajax/page"+currentpage+".html");
	$(".pager li").removeClass("active");
	$(this).parent("li").addClass("active");

  });

$(".next").click(function(){
	currentpage=currentpage+1;
if(currentpage>=maxpage)
{
$(".next").parent("li").addClass("disabled");
}
else
{
$(".next").parent("li").removeClass("disabled");
}
	if(currentpage>1)
	{
	$(".prev").parent("li").removeClass("disabled");
	}
		else
		{$(".prev").parent("li").addClass("disabled");
		}
		$(".load").parent("li").removeClass("active");
	$(".p"+currentpage).parent("li").addClass("active");
				$(".paging").load("ajax/page"+currentpage+".html");
});

$(".prev").click(function(){

	if(currentpage>1)
	{
	currentpage=currentpage-1;
		$(".load").parent("li").removeClass("active");
	$(".p"+currentpage).parent("li").addClass("active");
	$(".paging").load("ajax/page"+currentpage+".html");
	}	if(currentpage>1)
	{
	$(".prev").parent("li").removeClass("disabled");
	}
		else
		{$(".prev").parent("li").addClass("disabled");
	}if(currentpage>=maxpage)
{
$(".next").parent("li").addClass("disabled");
}
else
{
$(".next").parent("li").removeClass("disabled");
}
});

});

