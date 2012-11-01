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
setTimeout(function(){$("#more").load("assets/more.html");},300);

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


/* slim scroll */

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
		$(document).ready(function(){
			$('.board-msg').slimScroll({
			  height: '404px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 10
			});		
			$(".more-btn1").click(function(){
				var ht=$(".temphtml").load("ajax/more.html", function(response, status, xhr){;
				$('.board-msg').append(response);
				$(".board-msg").slimScroll({scroll: '50px' });
		       });
			});

        });						   
});

/* studentkit-student-page */

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
		$(document).ready(function(){
			$('.studnt-page-scorll').slimScroll({
			  height: '272px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 6
			});		
			$("a.scroll-more").click(function(){
				$(".temphtml").load("ajax/more1.html", function(response, status, xhr){
					$('.studnt-page-scorll').append(response);
				$(".studnt-page-scorll").slimScroll({scroll: '50px' });
				});
				
			});

        });						   
});

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
		$(document).ready(function(){
			$('.studnt-page-scorll1').slimScroll({
			  height: '243px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 6
			});		
			$("a.studnt-page-scorll-2").click(function(){
				$(".temphtml2").load("ajax/more1.html", function(response, status, xhr) {
					$('.studnt-page-scorll1').append(response);
					$(".studnt-page-scorll1").slimScroll({scroll: '50px' });
				});
				
			});

        });						   
});

/*  live join subject page */

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
	
			$('.my-subject-box').slimScroll({
			  height: '155px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 10
			});		
			$("a.mysubject-more").click(function(){
				$(".mysubjectbox-temp").load("ajax/mysubject.html", function(response, status, xhr) {
							$('.subject-box').append(response);
							$(".my-subject-box").slimScroll({scroll: '50px' });												 
				 });
	
				
			});
  });

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
	
			$('.up-coming').slimScroll({
			  height: '172px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 6
			});		
			$("a.lesson-more").click(function(){
				$(".mysubjectbox-temp").load("ajax/mysubject.html", function(response, status, xhr) {
							$('.subject-morelesson').append(response);
							$(".up-coming").slimScroll({scroll: '50px' });												 
				 });
	
				
			});
  });

/* student subject page */

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
	
			$('.teacherbox').slimScroll({
			  height: '525px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 6
			});		
			$("a.teacher-more").click(function(){
				$(".mysubjectbox-temp").load("ajax/teacher-profile.html", function(response, status, xhr) {
							$('.teacher-box2').append(response);
							$(".teacherbox").slimScroll({scroll: '50px' });												 
				 });
	
				
			});
  });

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
		$(document).ready(function(){
			$('.scorllbox').slimScroll({
			  height: '300px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 10
			});		
			$(".message-tm-more").click(function(){
				var ht=$(".temphtml").load("ajax/more.html", function(response, status, xhr){;
				$('.scorllbox').append(response);
				$(".scorllbox").slimScroll({scroll: '50px' });
		       });
			});

        });						   
});

$(document).ready(function(){
	function changeTime(spanId,val){
			document.getElementById(spanId).innerHTML=	val;
        }
		// For Search Selectbox
		$(document).ready(function(){
			$('.message-tm-stu').slimScroll({
			  height: '300px',
			  alwaysVisible: false,
			  start: 'bottom',
			  wheelStep: 10
			});		
			$(".message-tm-more").click(function(){
				var ht=$(".temphtml").load("ajax/more.html", function(response, status, xhr){;
				$('.message-tm-stu').append(response);
				$(".message-tm-stu").slimScroll({scroll: '50px' });
		       });
			});

        });						   
});


/* loadign */



$(document).ready(function(){

 hideLoading();
$(".upload-icon").click( function(){
$(".loadingbox").show();
var t=setTimeout(" hideLoading();",5000);

});

});
function hideLoading()
{
	$(".loadingbox").hide();
}