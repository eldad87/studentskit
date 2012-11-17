// JavaScript Document

/* star rating */

$(document).ready(function(){
    $('.star-box.dynamic').live("mouseover",function(){
        $(this).addClass('star-active');
        $(this).prevAll().addClass('star-active');
    });
    $('.star-box.dynamic').live("mouseout",function(){
        $(this).removeClass('star-active');
        $(this).prevAll().removeClass('star-active');


    });
    $('.star-box.dynamic').live("click", function(){
        if ($(this).hasClass('star-active1'))
        {
            if(($(this).attr("id"))==1){
                if(!$(this).nextAll().hasClass('star-active1'))
                {

                    $(this).removeClass('star-active1');
                }
            }
            $(this).nextAll().removeClass('star-active1');
        }
        else
        {
            $(this).addClass('star-active1');
            $(this).prevAll().addClass('star-active1');
        }
    });

});

/////////////////////////////////////////////////////////////// Helpers
/**
 * Used to post a form and replace its container with the (HTML) result
 * @constructor
 */
function PostForm() {
    this.forms = {};
    this.callbacks = {before:{}, after:{}, beforeAjax:{}};
}

PostForm.prototype.setAppendCallback = function( formSelector, on, func ) {
    this.callbacks[on][formSelector] = func;
}
PostForm.prototype.getAppendCallback = function( formSelector, on ) {
    if(this.callbacks[on][formSelector]) {
        return this.callbacks[on][formSelector];
    }

    return false;
}

/**

 *
 * Please note, on ajax pages (I.e student's profile). the loading JS command must exists in it:
 *  pfObj.loadForm('#user-profile-form', '#main-area', 'post');
 *  otherwise - the JS will word only for the first time.
 *
 * @param formSelector
 * @param appendResultsSelector
 * @param type
 * @param params
 */
PostForm.prototype.loadForm = function( formSelector, appendResultsSelector, type, params ) {
    var postForm = this;

    //Remove all event binding
    $(formSelector).unbind();

    $(formSelector).submit(function(event) {


        //Get form action (url)
        url = $(this).attr('action');

        if(!params) {
            params = $(this).data();
        }
        //Append data-* as hidden:input
        formData = $(this).data();
        $.each(formData, function(key, val){
            $('<input>').attr('type','hidden').attr('name', key).attr('value', val).appendTo(formSelector);
        });

        //Before we call Ajax
        beforeAjaxCallback = postForm.getAppendCallback(formSelector, 'beforeAjax');
        if(beforeAjaxCallback) {
            data = beforeAjaxCallback( data );
            if(!data) {
                return false;
            }
        }

        $.ajax({
            url: jQuery.nano(url, params),
            type: type,
            data: $(this).serialize(),
            dataType: 'html'

        }).done(function ( data ) {
                beforeCallback = postForm.getAppendCallback(formSelector, 'before');
                if(beforeCallback) {
                    data = beforeCallback( data );
                    if(!data) {
                        return false;
                    }
                }

                //replace the returned HTML with the current one
                $(appendResultsSelector).html(data);

                afterCallback = postForm.getAppendCallback(formSelector, 'after');
                if(afterCallback) {
                    afterCallback( data );
                }
            });

        //Stop from from submitting itself
        return false;
    });
}


/**
 * Used to post the form data, if any erros - append them
 */

function PostAPI() {
    this.forms = {};
    this.callbacks = {before:{}, after:{}, beforeAjax:{}};
}

PostAPI.prototype.setAppendCallback = function( formSelector, on, func ) {
    this.callbacks[on][formSelector] = func;
}
PostAPI.prototype.getAppendCallback = function( formSelector, on ) {
    if(this.callbacks[on][formSelector]) {
        return this.callbacks[on][formSelector];
    }

    return false;
}


PostAPI.prototype.loadElement = function( formSelector, onEvent, appendErrorsSelector, type ) {
    var postFormAPI = this;

    //Remove all event binding
    $(formSelector).unbind();

    $(formSelector).on(onEvent, function(event) {


        //Link data-* attributes will be used as data
        urlParams = $(this).data();
        dataParams = {};

        //Check if this is a form
        if($(this).attr('action')) {

            //Convert the data-* into hidden inputs
            if(urlParams) {
                form = $(this);
                $.each(urlParams, function(key, val){
                    $('<input>').attr('type','hidden').attr('name', key).attr('value', val).appendTo(form);
                });
            }


            //Form inputs will be used as data
            dataParams = $(this).serialize();

            //Get form action - this will be used as url
            url = $(this).attr('action');

        } else {
            //data-target
            url = urlParams['target'];

            dataParams = urlParams;
        }

        beforeAjaxCallback = postFormAPI.getAppendCallback(formSelector, 'beforeAjax');
        if(beforeAjaxCallback) {
            data = beforeAjaxCallback( data );
            if(!data) {
                return false;
            }
        }

        $.ajax({
            url: jQuery.nano(url, urlParams),
            type: type,
            data: dataParams,
            dataType: 'json'

        }).done(function ( data ) {
                beforeCallback = postFormAPI.getAppendCallback(formSelector, 'before');
                if(beforeCallback) {
                    data = beforeCallback( data );
                    if(!data) {
                        return false;
                    }
                }

                if(data['response']['title'][0]=='Error') {
                    //Show error
                    var msg = '';

                    var validationErrors = {};
                    if(data['response']['validation_errors']!=undefined) {
                        validationErrors = data['response']['validation_errors'];
                    } else if(data['response']['results'] && data['response']['results']['validation_errors']!=undefined) {
                        validationErrors = data['response']['results']['validation_errors'];
                    }

                    $.each(validationErrors, function(key, val) {
                        msg += val[0] + '<br />';
                    });
                    showError(appendErrorsSelector, data['response']['description'][0], msg);
                    return false;
                }

                afterCallback = postFormAPI.getAppendCallback(formSelector, 'after');
                if(afterCallback) {
                    afterCallback( data );
                }
            });

        //Stop from from submitting itself
        return false;
    });
}

/////////////////////////////////////////////////////////////// panel + site

function initSubjectForm(oneOnOnePriceInputSelector, lessonTypeInputSelector,
                         maxStudentsInputSelector, maxStudentsDivSelector,
                         fullGroupStudentPriceDivSelector, fullGroupStudentPriceInputSelector) {

    //Make Full-group-student-price invisible until the user set max-students>1
    $(fullGroupStudentPriceDivSelector).hide();

    //1. User change the value of 1on1 price
    $(oneOnOnePriceInputSelector).change(function(){
        if($(this).val()>0 && $(maxStudentsInputSelector).val()>1) {
            $(fullGroupStudentPriceDivSelector).show();
        } else {
            $(fullGroupStudentPriceDivSelector).hide();
        }

        //If there is no group price or group price is higher then 1 on 1 price
        if($(fullGroupStudentPriceInputSelector).val()=='' || $(fullGroupStudentPriceInputSelector).val()>$(this).val()) {

            //Set 1on1 price on group price
            $(fullGroupStudentPriceInputSelector).val($(this).val());
        }

    });

    //Make sure that the group price is equal or lower then 1on1 price
    $(fullGroupStudentPriceInputSelector).change(function(){
        if($(this).val()>$(oneOnOnePriceInputSelector).val()) {
            $(this).val($(oneOnOnePriceInputSelector).val());
        }
    });

    //Show/Hide student full group price - by max-students
    $(maxStudentsInputSelector).change(function(){
        //Show group student price
        if($(this).val()>1 && $(oneOnOnePriceInputSelector).val()>0) { //show it only if 1on1price>0
            $(fullGroupStudentPriceDivSelector).show();

            //Hide it
        } else {
            $(fullGroupStudentPriceDivSelector).hide();
        }
    });

    //If lesson type is video, hide max students and full-group-price
    $(lessonTypeInputSelector).change(function(){
        if($(this).val()=='live') {
            $(maxStudentsDivSelector).show();

            //file max-students change
            $(maxStudentsInputSelector).trigger('change');

        } else {
            $(maxStudentsDivSelector).hide();
            $(fullGroupStudentPriceDivSelector).hide();
        }
    });
}

function resetData(elementSelector) {
    if($(elementSelector).data()) {
        //console.log($(elementSelector).data());
        $.each($(elementSelector).data(), function(key, val){
            $(elementSelector).removeData(key);
        });
    }
}
/////////////////////////////////////////////////////////////// panel

/* Back office tabs */

//Old Tabs
$(document).ready(function(){
    $(".load").live("click",function(){
        $(".loadpage").load("/ajax/"+$(this).attr('rel'));
        $(".booking-nav li").removeClass("active");
        $(this).parent("li").addClass("active");
    });
});
//New Tabs
$(document).ready(function(){
    $(".load3").live("click",function(){
        $(".loadpage").load($(this).attr('rel'));
        $(".booking-nav li").removeClass("active");
        $(this).parent("li").addClass("active");
    });
});


//Organizer - Old menu link
$(document).ready(function(){
    $(".load1").live("click",function(){
        $("#main-area").load("/ajax/"+$(this).attr('rel'));
        $(".right-menu li").removeClass("bg-active");
        $(this).parent("li").addClass("bg-active");
    });

});
//Organizer - New menu links
function initMenuLinks() {
    $(".load2").unbind();
    $(".load2").live("click",function(){
        $(".loadpage1").load($(this).attr('rel'));
        $(".right-menu li").removeClass("bg-active");
        $(this).parent("li").addClass("bg-active");
    });
}
$(document).ready(function(){
    initMenuLinks();
});

function initTabs() {

    $(".tab-menu li").each(function(){
        if($(this).hasClass("active")){
            //	alert();
            $(".loadpage").load($(this).children("a").attr("rel"));
        }
    });

    return true;
}

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


//Boxes action buttons
function ActionButtons() {
    this.buttonSelectors = {};
    this.callbacks = {before:{}, after:{}};
}
ActionButtons.prototype.setCallback = function( buttonSelector, on, func ) {
    this.callbacks[on][buttonSelector] = func;
}
ActionButtons.prototype.getdCallback = function( buttonSelector, on ) {
    if(this.callbacks[on][buttonSelector]) {
        return this.callbacks[on][buttonSelector];
    }

    return false;
}
ActionButtons.prototype.loadButton = function(buttonSelector, url, type, params) {
    var actionButton = this;

    $(buttonSelector).click(function(event) {
        event.preventDefault();

        beforeCallback = postFormAPI.actionButton(buttonSelector, 'before');
        if(beforeCallback) {
            if(!beforeCallback( data )) {
                return false;
            }
        }

        $.ajax({
            url: jQuery.nano(url, params),
            type: type,
            data: params,
            dataType: 'json'

        }).done(function ( data ) {

                if(data['response']['title'][0]=='Error') {
                    //Show error
                    var msg = '';
                    $.each(data['response']['validation_errors'], function(key, val) {
                        msg += val[0] + '<br />';
                    });
                    showError(appendErrorsSelector, data['response']['description'][0], msg);

                }

                afterCallback = postFormAPI.getAppendCallback(buttonSelector, 'after');
                if(afterCallback) {
                    afterCallback( data );
                }
            });

        //Stop from from submitting itself
        return false;
    });
}


var pfObj = new PostForm();
var pAPIObj = new PostAPI();




/////////////////////////////////////////////////////////////// site



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

LoadMore.prototype.loadMoreButton = function(buttonSelector, eventName, appendToSelector, url, params, type, limit, excludeGetParams) {

    var loadMoreObj = this;

    $(buttonSelector).bind(eventName ,function() {

        params['rnd'] = Math.random(); //to avoid cache
        params['limit'] = limit;
        params['page'] = loadMoreObj.getNextPage(buttonSelector);

        if(excludeGetParams) {
            for(i in excludeGetParams) {
                delete params[excludeGetParams[i]];
            }
        }

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

function initToolTips() {
    $("[rel=tooltip]").tooltip({html: true});
}

$(document).ready(function(){
    //Activate tooltip
    initToolTips();

    //Disable links anchor default <a href="#">..
    //Without it - the user screen will jump
    $('a[href="#"]').click(function(e) {
        e.preventDefault();
    });
});
///////////////////////////////////////////// Login/registration management

$(document).ready(function(){

    //Make sure the user logged in
    $.ajaxSetup({
        error: function(event, request, options, error) {
            switch (event.status) {
                case 403: //Forb    idden - caused by users that not logged in
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
                    showError('#register-form .modal-body', data['response']['description'][0], msg);

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

///////////////////////////////////////////// Order load more join lessons
$(document).ready(function(){
    /* Order join load more */

    //Scroll
    $('#upcoming .modal-body').slimScroll({
        height: '200px',
        start: 'top',
        disableFadeOut: true
    });

    var url = '/Order/getUpcomingOpenLessonForSubject/{subject_id}/{limit}/{page}';

    lmObj.loadMoreButton('.upcoming-lessons-for-subject', 'click', '#upcoming .modal-body', url, jsSettings, 'get', 3);
});

///////////////////////////////////////////// Home/Requests auto complete
$(document).ready(function(){
    $( "#term" ).autocomplete({ source: jsSettings['search_suggestions_url'] });
});

///////////////////////////////////////////// Home/Requests search load more
$(document).ready(function(){
    /* Home/Requests search load more */

    var url = '{search_load_more_url}&limit={limit}&page={page}';

    lmObj.loadMoreButton('.search-load-more', 'click', 'ul.lesson-container', url, jsSettings, 'get', 8);
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

///////////////////////////////////////

/* Country, TZ, Lang script */
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