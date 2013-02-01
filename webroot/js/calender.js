$(document).ready(function(){
     jfcalplugin = $(calendarSelector).jFrontierCal({
        date: new Date(),
        dayClickCallback:           myDayClickHandler,
        agendaClickCallback:        myAgendaClickHandler,
        applyAgendaTooltipCallback: myApplyTooltip,
        dragAndDropEnabled: false
    }).data("plugin");

    jfcalplugin.setAspectRatio(calendarSelector, 0.75);



    /**
     * Called when user clicks day cell
     * use reference to plugin object to add agenda item
     */
    function myDayClickHandler(eventObj){
        // Get the Date of the day that was clicked from the event object
        var date = eventObj.data.calDayDate;

        var now = new Date();
        //Check if today/future date
        if(date>=now ||
            //Check if same date
                (date.getFullYear()==now.getFullYear() &&
                 date.getMonth()==now.getMonth() &&
                 date.getDate()==now.getDate() ))
        {
            //Set popup with date
            $('#startYear').val(
                date.getFullYear()
            );
            $('#startMonth').val(
                date.getMonth()+1
            );
            $('#startDay').val(
                date.getDate()
            );

            $('#add-event-form').dialog('open');
        } else {
            showError('#calendar-msg', 'Error' ,'Please select a future date');
        }
    };


    /**
     * Called when user clicks and agenda item
     */
    function myAgendaClickHandler(eventObj){
        // Get ID of the agenda item from the event object
        var agendaId = eventObj.data.agendaId;
        // pull agenda item from calendar
        var agendaItem = jfcalplugin.getAgendaItemById(calendarSelector, agendaId);



        //Check if lesson already started
        var now = new Date();
        if(agendaItem.startDate<now){
            return false;
        }

        //Check if its a blocked item
        if(!agendaItem.data) {
            return false;
        }

        if(!jsSettings['calendarClickUrl']) {
            return false;
        }
        //Open a new lesson page
        window.open(
            $.nano(jsSettings['calendarClickUrl'], agendaItem.data)
        );
    };

    /**
     * Draw the current year and month
     */
    function showCalDate() {
        var calDate = jfcalplugin.getCurrentDate(calendarSelector); // returns Date object
        $("#monthDisplay").html(months[calDate.getMonth()]);
        $("#yearDisplay").html(calDate.getFullYear());
    }


    //Draw the current month
    showCalDate();

    ///////////////////////////////////////////////////////////////////////////////////////////////////////


    //init next button
    $("#BtnPreviousMonth").click(function() {
        jfcalplugin.showPreviousMonth(calendarSelector);
        showCalDate();
    });

    //init next button
    $("#BtnNextMonth").click(function() {
        jfcalplugin.showNextMonth(calendarSelector);
        showCalDate();
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Custom tooltip - use any tooltip library you want to display the agenda data.
     * for this example we use qTip - http://craigsworks.com/projects/qtip/
     *
     * @param divElm - jquery object for agenda div element
     * @param agendaItem - javascript object containing agenda data.
     */
    function myApplyTooltip(divElm, agendaItem){
        // Destroy current tooltip if present
        if(divElm.data("qtip")){
            divElm.qtip("destroy");
        }

        //locked item
        if(!agendaItem.data) {
            return false;
        }

        //Convert image to <img> element
        agendaItem.data.image = $.nano(
            '<img src="{image}" class="space1 pull-left" />',
            agendaItem.data
        );

        var displayData = $.nano(
            $('#tooltip-template').html(), agendaItem.data
        );


        divElm.qtip({
            content: displayData,
            position: {
                corner: {
                    tooltip: "topMiddle",
                    target: "topMiddle"
                },
                adjust: {
                    mouse: true,
                    x: 0,
                    y: 15
                },
                target: "mouse"
            },
            show: {
                when: {
                    event: 'mouseover'
                }
            },
            style: {
                border: {
                    width:1,
                    radius: 0,
                    color:'#cccccc'
                },
                padding: 0,
                textAlign: "left",
                tip: true,
                backgroundColor: '#FFFFFF',
                color: '#333333'
                // other style properties are inherited from dark theme
            }
        });
    }

    /**
     * Initialize add event modal form
     */
    jQuery("#add-event-form").dialog({
        autoOpen: false,
        width: 300,
        modal: true,
        resizable: false,
        buttons: {
            'Submit': function() {
                //Submit
                $('#setLessonDatetime').submit();
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        },
        close: function() {
            jQuery("#startHour option:eq(0)").attr("selected", "selected");
            jQuery("#startMin option:eq(0)").attr("selected", "selected");
            //jQuery("#startMeridiem option:eq(0)").attr("selected", "selected");
        }
    });

    jQuery("#error").dialog({
        autoOpen: false,
        height: 400,
        width: 400,
        resizable: false,
        modal: true,
        Cancel: function() {
            $(this).dialog('close');
        }
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////////// Helpers

function addAgenda( teacherLessonId,
                    title, description,
                    currentStudentsCount, maxStudentsCount,
                    startDate, durationMin,
                    image,
                    lessonType) {

    var sDate = new Date(startDate);
    var eDate = new Date(sDate);
    eDate.setMinutes( eDate.getMinutes()+durationMin );


var name = '';
    //userLessons
    var backgroundColor;
    if(currentStudentsCount<maxStudentsCount) {
        if(lessonType=='TeacherLesson') {
            backgroundColor = colors.openTeacherLesson;
        } else {
            backgroundColor = colors.openUserLessons;
        }
    } else {
        backgroundColor = colors.closed;
    }

    jfcalplugin.addAgendaItem(
        calendarSelector,
        title,
        sDate,
        eDate,
        false,
        {
            teacher_lesson_id: teacherLessonId,
            title: name +title,
            description: description,
            startDate: startDate,
            durationMin: durationMin,
            currentStudentsCount: currentStudentsCount,
            maxStudentsCount: maxStudentsCount,
            image: image
        },
        {
            backgroundColor: backgroundColor
        }
    );
}

function addBlockedAgenda(startDate, durationMin) {
    var sDate = new Date(startDate);
    var eDate = new Date(sDate);
    eDate.setMinutes( eDate.getMinutes()+durationMin );

    jfcalplugin.addAgendaItem(
        calendarSelector,
        '',
        sDate,
        eDate,
        false,
        {},
        {
            backgroundColor: colors.closed
        }
    );

}


