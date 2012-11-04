// JavaScript Document





/*  live join subject page */
function LoadMore() {
    this.paginator = {};
    this.callbacks = {before:{}, after:{}};
}
LoadMore.prototype.getNextPage = function(buttonSelector) {
    if(!this.paginator[buttonSelector]) {
        this.paginator[buttonSelector] = 1;
    }

    this.paginator[buttonSelector]++;
    return this.paginator[buttonSelector];
}
LoadMore.prototype.curlyBracketsVars = function(url, params) {

    var curlyRe = /\{(.*?)}/g;
    url = url.replace(curlyRe, function() {
        if(!params[arguments[1]]) {
            return '{' + arguments[1] + '}';
        }
        return params[arguments[1]];
    });

    return url;
}
LoadMore.prototype.loadMoreButton = function(buttonSelector, eventName, appendToSelector, url, params, type, limit) {
    params['rnd'] = Math.random(); //to avoid cache

    params['limit'] = limit;
    var loadMoreObj = this;

    $(buttonSelector).bind(eventName ,function() {

        params['page'] = loadMoreObj.getNextPage(buttonSelector);

        $.ajax({
            url: jQuery.nano(url, params), //loadMoreObj.curlyBracketsVars(url, params) + '.json',
            type: type,
            data: params,
            dataType: 'html'

        }).done(function ( data ) {
            beforeCallback = loadMoreObj.getAppendCallback(buttonSelector, 'before');
            if(beforeCallback) {
                data = beforeCallback( data );
                if(!data) {
                    return false;
                }
            } else {
                //Default action
                if(!data) {
                    $(buttonSelector).css('visibility', 'hidden');
                }
            }
            $(data).appendTo(appendToSelector);

            afterCallback = loadMoreObj.getAppendCallback(buttonSelector, 'after');
            if(afterCallback) {
                afterCallback( data );
            }
        });

     });

};
LoadMore.prototype.setAppendCallback = function( buttonSelector, on, func ) {
    this.callbacks[on][buttonSelector] = func;
}
LoadMore.prototype.getAppendCallback = function( buttonSelector, on ) {
     if(this.callbacks[on][buttonSelector]) {
         return this.callbacks[on][buttonSelector];
     }

    return false;
}

var lmObj = new LoadMore();

function showError(inSelector, title, msg) {
    $(inSelector + ' .alert').remove(); //Remove old alert msg
    if(title==undefined || msg==undefined) {
        return false;
    }
    $(inSelector).prepend('<div class="alert fade in"> <button type="button" class="close" data-dismiss="alert">×</button> <strong>'+ title +' </strong>'+ msg +'</div>'); //Append new alert msg
}

//Copy ids from A to model
function initCopyIdLinks() {
    $(".copyDataId").click(function () {
        $($(this).data('hidden-input')).val($(this).data('id'))
    });
}

///////////////////////////////////////////// Login/registration management

$(document).ready(function(){

    //Make sure the user logged in
    $.ajaxSetup({
        error: function(event, request, options, error) {
            switch (event.status) {
                case 403: //Forbidden - caused by users that not logged in
                    $('#login-popup').modal('show');
                break;
            }
        }
    });

    //Make sure .requireLogin elements will popup the login-form first and cancel other event-listeners
    $('.requireLogin').click(function() {
        if(!jsSettings['user_id']) {
            //TODO: make sure this is the first event
            $('#login-popup').modal('show');
            return false;
        }
    });

    //Login form JS
    $('#login-form').submit(function() {
        $.post(
            '/login.json',
            $(this).serialize(),
            function(data){
                if(data['response']['title'][0]=='Error') {
                    //Show error
                    showError('#login-form .modal-body', data['response']['title'][0], data['response']['description'][0]);

                } else {
                    //Login Success
                    jsSettings['user_id'] = data['response']['user_id'];
                    updateTopBar();
                    $('#login-popup').modal('hide');
                }
            }
        );

        return false;
    });

    //Registration form JS
    $('#register-form').submit(function() {
        $.post(
            '/register.json',
            $(this).serialize(),
            function(data){
                if(data['response']['title'][0]=='Error') {
                    //Show error
                    var msg = '';
                    $.each(data['response']['validation_errors'], function(key, val) {
                        msg += val[0] + '<br />';
                    });
                    showError('#login-form .modal-body', data['response']['description'][0], msg);

                } else {
                    //Login Success
                    jsSettings['user_id'] = data['response']['user_id'];
                    updateTopBar();
                    $('#register-popup').modal('hide');
                }
            }
        );

        return false;
    });

    function updateTopBar() {
        //TODO:
        //location.reload(); //Reload page
    }
});


///////////////////////////////////////////// Teacher/TeacherSubject page
$(document).ready(function(){
    /* My Subject */

    //Scroll
    $('.my-subject-box').slimScroll({
        height: '159px',
        start: 'top',
        width: '100%',
        disableFadeOut: true
    });

    var url = '/Home/getTeacherSubjects/{teacher_user_id}/{limit}/{page}';
    if(jsSettings['subject_id']) {
        url = url + '/{subject_id}'
    }

    lmObj.loadMoreButton('.mysubject-more', 'click', '.subject-box', url, jsSettings, 'get', 3);
});

$(document).ready(function(){
    /* Upcoming lessons */

    //Scroll
    $('div.up-coming').slimScroll({
        height: '90px',
        start: 'top',
        width: '100%',
        disableFadeOut: true
    });

    var url = '/Home/getUpcomingOpenLesson/{teacher_user_id}/{limit}/{page}';
    if(jsSettings['subject_id']) {
        url = '/Home/getUpcomingOpenLessonForSubject/{subject_id}/{limit}/{page}';
    }

    lmObj.loadMoreButton('a.upcoming-more', 'click', 'ul.upcoming-more', url, jsSettings, 'get', 3);
});

$(document).ready(function(){
    /* Reviews by students for teacher/teacherSubject pages */

    $(document).ready(function(){
        $('div.reviews-by-students').slimScroll({
            height: '135px',
            width: '100%',
            start: 'top'
        });

        var url = '/Home/getTeacherRatingByStudents/{teacher_user_id}/{limit}/{page}';
        if(jsSettings['subject_id']) {
            var url = '/Home/getTeacherRatingByStudentsForSubject/{subject_id}/{limit}/{page}';
        }

        lmObj.loadMoreButton('a.reviews-by-students', 'click', 'div.reviews-by-students', url, jsSettings, 'get', 3);
    });
});

///////////////////////////////////////////// User page
$(document).ready(function(){
    /* Reviews by teachers for user page */

    $(document).ready(function(){
        $('div.reviews-by-teachers').slimScroll({
            height: '135px',
            width: '100%',
            start: 'top'
        });

        lmObj.loadMoreButton('a.reviews-by-teachers', 'click', 'div.reviews-by-teachers', '/Home/getStudentRatingByTeachers/{student_user_id}/{limit}/{page}', jsSettings, 'get', 3);
    });
});

$(document).ready(function(){
    /* user latest lessons */

    //Scroll
    $('div.latest-lessons').slimScroll({
        height: '115px',
        alwaysVisible: false,
        start: 'top',
        wheelStep: 6
    });

    lmObj.loadMoreButton('a.latest-lessons', 'click', 'div.latest-lessons', '/Home/getStudentArchiveLessons/{student_user_id}/{limit}/{page}', jsSettings, 'get', 5);
});

///////////////////////////////////////////// Home page
$(document).ready(function() {
    /* Home last board posts */

    //Scroll
    $('.board-msg').slimScroll({
        height: '404px',
        alwaysVisible: false,
        start: 'top'
    });

    lmObj.loadMoreButton('a.more-btn1', 'click', 'ul.board-msg', '/Home/latestBoardPosts/{limit}/{page}', jsSettings, 'get', 5);
});


/////////////////////////////////////// Teacher/User panel
$(document).ready(function(){
    /* homepage - show latest board messages */

    // For Search Selectbox
    $(document).ready(function(){
        $('.message-tm-stu').slimScroll({
              height: '300px',
              alwaysVisible: false,
              start: 'bottom',
              wheelStep: 10
        });
        $(".message-tm-more").click(function(){
                var ht=$(".temphtml").load("/ajax/more.html", function(response, status, xhr){;
                    $('.message-tm-stu').append(response);
                    $(".message-tm-stu").slimScroll({scroll: '50px' });
                });
        });

    });
});




/////////////////////////////////////// WTF?
/* student subject page


 $(document).ready(function(){
 // For Search Selectbox
 $(document).ready(function(){
 $('.scorllbox').slimScroll({
 height: '300px',
 alwaysVisible: false,
 start: 'bottom',
 wheelStep: 10
 });
 $(".message-tm-more").click(function(){
 var ht=$(".temphtml").load("/ajax/more.html", function(response, status, xhr){;
 $('.scorllbox').append(response);
 $(".scorllbox").slimScroll({scroll: '50px' });
 });
 });

 });
 });*/

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


/* Ajax tab script */

$(document).ready(function(){
    $(".load").live("click",function(){
        $(".loadpage").load("/ajax/"+$(this).attr('rel'));
        $(".booking-nav li").removeClass("active");
        $(this).parent("li").addClass("active");
    });

});
$(document).ready(function(){
    $(".load1").live("click",function(){
        $(".loadpage1").load("/ajax/"+$(this).attr('rel'));
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
        $(".paging").load("/ajax/page"+currentpage+".html");
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
        $(".paging").load("/ajax/page"+currentpage+".html");
    });

    $(".prev").click(function(){

        if(currentpage>1)
        {
            currentpage=currentpage-1;
            $(".load").parent("li").removeClass("active");
            $(".p"+currentpage).parent("li").addClass("active");
            $(".paging").load("/ajax/page"+currentpage+".html");
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