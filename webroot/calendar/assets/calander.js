// JavaScript Document
/* variable for events
 event_status for type of the event
 color_arr for color of event type

 */
jQuery(document).ready(function(){
    var event_status=1;
    var color_arr=new Array("#000","#299FE0","#b6b6b6","#07B939"); // stutas color
    /*event_status		0-Closed
     1-Open
     2-Teacher approval
     3-Auto approve*/
    var event_duration=60;	// event end after duration in minutes



    var sysdate = new Date();
    var sysmon = sysdate.getMonth();
    var sysday=sysdate.getDate();
    var sysyear = sysdate.getFullYear();
    var tm=new Date(sysyear,sysmon,sysday);
    var h= sysdate.getHours();
    var m= sysdate.getMinutes();
    var sdate=sysdate;
    sdate.setHours(sdate.getHours()+1);
    var clickDate = "";
    var clickAgendaItem = "";
    /**
     * Initializes calendar with current year & month
     * specifies the callbacks for day click & agenda item click events
     * then returns instance of plugin object
     */
    var jfcalplugin = $("#mycal").jFrontierCal({
        date: new Date(),
        dayClickCallback: myDayClickHandler,
        agendaClickCallback: myAgendaClickHandler,
        agendaDropCallback: myAgendaDropHandler,
        agendaMouseoverCallback: myAgendaMouseoverHandler,
        applyAgendaTooltipCallback: myApplyTooltip,
        agendaDragStartCallback : myAgendaDragStart,
        agendaDragStopCallback : myAgendaDragStop,
        dragAndDropEnabled: true
    }).data("plugin");

    jfcalplugin.addAgendaItem(

        "#mycal",
        "Christmas Eve",

        new Date(sysyear,sysmon,sysday,20,00,00,00),
        new Date(sysyear,sysmon,sysday,23,59,59,999),
        false,
        {
            fname: "Santa",
            lname: "Claus",
            leadReindeer: "Rudolph",
            myExampleDate:sysyear
        },
        {
            backgroundColor: "#000000",
            foregroundColor: "#ffffff"
        }
    );
    function myDayClickHandler(eventObj){
        var date = eventObj.data.calDayDate;
    };
    function myAgendaClickHandler (eventObj){
        var agendaId = eventObj.data.agendaId;
        var item = jfcalplugin.getAgendaItemById("#mycal",agendaId);
    };
    function myAgendaDropHandler(eventObj){
        var agendaId = eventObj.data.agendaId;
        var date = eventObj.data.calDayDate;
        var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
        alert("You dropped agenda item " + agendaItem.title +
            " onto " + date.toString() + ". Here is where you can make an AJAX call to update your database.");
    };

// retrieve agend item by data attribute
    var item = jfcalplugin.getAgendaItemByDataAttr("#mycal","fname","Indiana");

// get all agenda items

    var a=sysday+8;
    var allItemsArray = jfcalplugin.getAllAgendaItems("#mycal");
    jfcalplugin.addAgendaItem(

        "#mycal",
        "Christmas Eve",

        new Date(sysyear,sysmon,a,20,00,00,00),
        new Date(sysyear,sysmon,a,23,59,59,999),
        false,
        {
            fname: "Santa",
            lname: "Claus",
            leadReindeer: "Rudolph",
            myExampleDate:sysyear
        },
        {
            backgroundColor: "#FF8C1C",
            foregroundColor: "#ffffff"
        }
    );
    function myDayClickHandler(eventObj){
        var date = eventObj.data.calDayDate;
    };
    function myAgendaClickHandler (eventObj){
        var agendaId = eventObj.data.agendaId;
        var item = jfcalplugin.getAgendaItemById("#mycal",agendaId);
    };
    function myAgendaDropHandler(eventObj){
        var agendaId = eventObj.data.agendaId;
        var date = eventObj.data.calDayDate;
        var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
        alert("You dropped agenda item " + agendaItem.title +
            " onto " + date.toString() + ". Here is where you can make an AJAX call to update your database.");
    };

// retrieve agend item by data attribute
    var item = jfcalplugin.getAgendaItemByDataAttr("#mycal","fname","Indiana");

// get all agenda items
    var allItemsArray = jfcalplugin.getAllAgendaItems("#mycal");
    /**
     * Do something when dragging starts on agenda div
     */
    function myAgendaDragStart(eventObj,divElm,agendaItem){
        // destroy our qtip tooltip
        if(divElm.data("qtip")){
            divElm.qtip("destroy");
        }
    };

    /**
     * Do something when dragging stops on agenda div
     */
    function myAgendaDragStop(eventObj,divElm,agendaItem){
        //alert("drag stop");
    };

    /**
     * Custom tooltip - use any tooltip library you want to display the agenda data.
     * for this example we use qTip - http://craigsworks.com/projects/qtip/
     *
     * @param divElm - jquery object for agenda div element
     * @param agendaItem - javascript object containing agenda data.
     */
    function myApplyTooltip(divElm,agendaItem){
        // Destroy currrent tooltip if present
        if(divElm.data("qtip")){
            divElm.qtip("destroy");
        }
        var displayData = "";
        var title = agendaItem.title;
        var startDate = agendaItem.startDate;

        var mnth = startDate.getMonth();
        var sday=startDate.getDate();
        var syear = startDate.getFullYear();
        var shr= startDate.getHours();
        var time_am="AM";
        if(shr>12){
            shr-=12;
            time_am="PM";
        }
        var smin= startDate.getMinutes();

        var endDate = agendaItem.endDate;

        var emnth = endDate.getMonth();
        var eday=endDate.getDate();
        var eyear = endDate.getFullYear();
        var ehr= endDate.getHours();
        var etime_am="AM";
        if(ehr>12){
            ehr-=12;
            etime_am="PM";
        }
        var emin= endDate.getMinutes();

        var allDay = agendaItem.allDay;
        var data = agendaItem.data;

        displayData += "<div class='popupwindow radius3'><div class='upr-box-tool'><h6 class='pull-left'><strong>Class Time</strong><br/><span class='fontsize3'>"+shr+":"+smin+" "+time_am+" to "+ehr+":"+emin+" "+etime_am+"</span></h6>";
        displayData +=	"<h6 class='pull-right right-text'><strong>Students</strong><br/><span class='fontsize3'>7/10</span></h6>";
        displayData +=	"</div>";
        displayData += "	<div class='bottom-box-tool'>";
        displayData += "<p>"+ title+"</p>";
        displayData += " </div></div>";

        // use the user specified colors from the agenda item.
        var backgroundColor = agendaItem.displayProp.backgroundColor;
        var foregroundColor = agendaItem.displayProp.foregroundColor;
        var myStyle = {
            border: {
                width:1,
                radius: 0,
                color:'#cccccc'
            },
            padding: 0,
            textAlign: "left",
            tip: true
            // other style properties are inherited from dark theme
        };
        /*if(backgroundColor != null && backgroundColor != ""){
         myStyle["backgroundColor"] = backgroundColor;
         }*/
        /*
         if(foregroundColor != null && foregroundColor != ""){
         myStyle["color"] = foregroundColor;
         }*/
        // apply tooltip

        myStyle["backgroundColor"] ='#FFFFFF';
        myStyle["color"] ="#333333";

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
            style: myStyle
        });

    };

    /**
     * Make the day cells roughly 3/4th as tall as they are wide. this makes our calendar wider than it is tall.
     */
    jfcalplugin.setAspectRatio("#mycal",0.75);

    /**
     * Called when user clicks day cell
     * use reference to plugin object to add agenda item
     */
    function myDayClickHandler(eventObj){
        // Get the Date of the day that was clicked from the event object
        var date = eventObj.data.calDayDate;
        // store date in our global js variable for access later
        clickDate = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
        // open our add event dialog
        //var ad=date.getDate();
        //alert(clickDate);
        if(date>=tm){
            $('#add-event-form').dialog('open');

        }
        else
        {
            //$('#error').dialog('open');
            alert("Sorry, even cant added in backdate");
        }

    };

    /**
     * Called when user clicks and agenda item
     * use reference to plugin object to edit agenda item
     */
    function myAgendaClickHandler(eventObj){
        // Get ID of the agenda item from the event object
        var agendaId = eventObj.data.agendaId;
        // pull agenda item from calendar
        var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
        clickAgendaItem = agendaItem;
        $("#display-event-form").dialog('open');
    };

    /**
     * Called when user drops an agenda item into a day cell.
     */
    function myAgendaDropHandler(eventObj){
        // Get ID of the agenda item from the event object
        var agendaId = eventObj.data.agendaId;
        // date agenda item was dropped onto
        var date = eventObj.data.calDayDate;
        // Pull agenda item from calendar
        var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
        alert("You dropped agenda item " + agendaItem.title +
            " onto " + date.toString() + ". Here is where you can make an AJAX call to update your database.");
    };

    /**
     * Called when a user mouses over an agenda item
     */
    function myAgendaMouseoverHandler(eventObj){
        var agendaId = eventObj.data.agendaId;
        var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
        //alert("You moused over agenda item " + agendaItem.title + " at location (X=" + eventObj.pageX + ", Y=" + eventObj.pageY + ")");
    };


    /**
     * Initialize jquery ui datepicker. set date format to yyyy-mm-dd for easy parsing
     */
    /*$("#dateSelect").datepicker({
     showOtherMonths: true,
     selectOtherMonths: true,
     changeMonth: true,
     changeYear: true,
     showButtonPanel: true,
     dateFormat: 'm/d/Y'
     });*/
    /* $.datepicker.show();*/
    /**
     * Set datepicker to current date
     */


    var nam;
    if(sysmon==0)
    {
        nam="January";
    }
    if(sysmon==1)
    {
        nam="Febuary";
    }
    if(sysmon==2)
    {
        nam="March";
    }
    if(sysmon==3)
    {
        nam="April";
    }
    if(sysmon==4)
    {
        nam="May";
    }
    if(sysmon==5)
    {
        nam="June";
    }
    if(sysmon==6)
    {
        nam="July";
    }
    if(sysmon==7)
    {
        nam="Augast";
    }
    if(sysmon==8)
    {
        nam="September";
    }
    if(sysmon==9)
    {
        nam="October";
    }
    if(sysmon==10)
    {
        nam="November";
    }
    if(sysmon==11)
    {
        nam="December";
    }


    $("#dateSelect").val(nam);
    $("#dateSelect1").val(sysyear);
    /**
     * Use reference to plugin object to a specific year/month
     */
    $("#dateSelect").bind('change', function() {
        var selectedDate = $("#dateSelect").val();
        var dtArray = selectedDate.split("-");
        var year = dtArray[0];
        // jquery datepicker months start at 1 (1=January)
        var month = dtArray[1];
        // strip any preceeding 0's
        month = month.replace(/^[0]+/g,"")
        var day = dtArray[2];
        // plugin uses 0-based months so we subtrac 1
        jfcalplugin.showMonth("#mycal",year,parseInt(month-1).toString());
    });
    /**
     * Initialize previous month button
     */
    $("#BtnPreviousMonth").button();
    $("#BtnPreviousMonth").click(function() {
        jfcalplugin.showPreviousMonth("#mycal");
        // update the jqeury datepicker value
        var calDate = jfcalplugin.getCurrentDate("#mycal"); // returns Date object
        var cyear = calDate.getFullYear();
        // Date month 0-based (0=January)
        var cmonth = calDate.getMonth();
        var cday = calDate.getDate();
        /* var d = new Date();
         var n = d.getMonth();*/
        var n=cmonth;
        var nam;
        if(n==0)
        {
            nam="January";
        }
        if(n==1)
        {
            nam="February";
        }
        if(n==2)
        {
            nam="March";
        }
        if(n==3)
        {
            nam="April";
        }
        if(n==4)
        {
            nam="May";
        }
        if(n==5)
        {
            nam="June";
        }
        if(n==6)
        {
            nam="July";
        }
        if(n==7)
        {
            nam="Augast";
        }
        if(n==8)
        {
            nam="September";
        }
        if(n==9)
        {
            nam="October";
        }
        if(n==10)
        {
            nam="November";
        }
        if(n==11)
        {
            nam="December";
        }
        // jquery datepicker month starts at 1 (1=January) so we add 1
        /*$("#dateSelect").datepicker("setDate",cyear+"-"+(cmonth+1)+"-"+cday);*/
        $("#dateSelect").val(nam);$("#dateSelect1").val(cyear);
        return false;
    });
    /**
     * Initialize next month button
     */
    $("#BtnNextMonth").button();
    $("#BtnNextMonth").click(function() {
        jfcalplugin.showNextMonth("#mycal");
        // update the jqeury datepicker value
        var calDate = jfcalplugin.getCurrentDate("#mycal"); // returns Date object
        var cyear = calDate.getFullYear();
        // Date month 0-based (0=January)
        var cmonth = calDate.getMonth();
        var cday = calDate.getDate();
        var n=cmonth;
        var nam;
        if(n==0)
        {
            nam="January";
        }
        if(n==1)
        {
            nam="February";
        }
        if(n==2)
        {
            nam="March";
        }
        if(n==3)
        {
            nam="April";
        }
        if(n==4)
        {
            nam="May";
        }
        if(n==5)
        {
            nam="June";
        }
        if(n==6)
        {
            nam="July";
        }
        if(n==7)
        {
            nam="Augast";
        }
        if(n==8)
        {
            nam="September";
        }
        if(n==9)
        {
            nam="October";
        }
        if(n==10)
        {
            nam="November";
        }
        if(n==11)
        {
            nam="December";
        }

        $("#dateSelect").val(nam);
        $("#dateSelect1").val(cyear);
        // jquery datepicker month starts at 1 (1=January) so we add 1
        /*$("#dateSelect").datepicker("setDate",cyear+"-"+(cmonth+1)+"-"+cday);	*/
        return false;
    });

    /**
     * Initialize delete all agenda items button
     */
    $("#BtnDeleteAll").button();
    $("#BtnDeleteAll").click(function() {
        jfcalplugin.deleteAllAgendaItems("#mycal");
        return false;
    });

    /**
     * Initialize iCal test button
     */
    $("#BtnICalTest").button();
    $("#BtnICalTest").click(function() {
        // Please note that in Google Chrome this will not work with a local file. Chrome prevents AJAX calls
        // from reading local files on disk.
        jfcalplugin.loadICalSource("#mycal",$("#iCalSource").val(),"html");
        return false;
    });

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
                var what = jQuery.trim($("#what").val());
                var aa=color_arr[event_status];
                if(what == ""){
                    alert("Please enter a short event description into the \"what\" field.");
                }else{

                    var startDate = $("#startDate").val();

                    var startDtArray = startDate.split("-");
                    var startYear = startDtArray[0];
                    // jquery datepicker months start at 1 (1=January)
                    var startMonth = startDtArray[1];
                    var startDay = startDtArray[2];
                    // strip any preceeding 0's
                    startMonth = startMonth.replace(/^[0]+/g,"");
                    startDay = startDay.replace(/^[0]+/g,"");
                    var startHour = jQuery.trim($("#startHour").val());
                    var startMin = jQuery.trim($("#startMin").val());
                    var startMeridiem = jQuery.trim($("#startMeridiem").val());
                    startHour = parseInt(startHour.replace(/^[0]+/g,""));
                    if(startMin == "0" || startMin == "00"){
                        startMin = 0;
                    }else{
                        startMin = parseInt(startMin.replace(/^[0]+/g,""));
                    }
                    if(startMeridiem == "AM" && startHour == 12){
                        startHour = 0;
                    }else if(startMeridiem == "PM" && startHour < 12){
                        startHour = parseInt(startHour) + 12;
                    }

                    var endDate = $("#endDate").val();
                    var endDtArray = endDate.split("-");
                    var endYear = endDtArray[0];
                    // jquery datepicker months start at 1 (1=January)
                    var endMonth = endDtArray[1];
                    var endDay = endDtArray[2];
                    // strip any preceeding 0's
                    endMonth = endMonth.replace(/^[0]+/g,"");

                    endDay = endDay.replace(/^[0]+/g,"");
                    /*var endHour = jQuery.trim($("#endHour").val());
                     var endMin = jQuery.trim($("#endMin").val());
                     var endMeridiem = jQuery.trim($("#endMeridiem").val());*/
                    var ehr=Math.round(event_duration/60);
                    var emin=event_duration-(ehr*60);

                    var endHour = jQuery.trim($("#startHour").val());
                    var endMin = jQuery.trim($("#startMin").val());
                    var endMeridiem = jQuery.trim($("#startMeridiem").val());
                    endHour = parseInt(endHour.replace(/^[0]+/g,""))+ehr;
                    if(endMin == "0" || endMin == "00"){
                        endMin = 0+emin;
                    }else{
                        endMin = parseInt(endMin.replace(/^[0]+/g,""))+emin;
                    }
                    //alert(endHour+" - "+endMeridiem);
                    if(endMeridiem == "AM" && endHour == 12){
                        endMeridiem = "PM";
                        endHour=12;
                    }
                    if(endMeridiem == "AM" && endHour == 13){
                        endHour = 1;
                    }else if(endMeridiem == "PM" && endHour < 12){
                        endHour = parseInt(endHour) + 12;
                    }
                    //alert(endHour+" - "+endMeridiem);
                    //alert("Start time: " + startHour + ":" + startMin + " " + startMeridiem + ", End time: " + endHour + ":" + endMin + " " + endMeridiem);
                    // Dates use integers
                    var startDateObj = new Date(parseInt(startYear),parseInt(startMonth)-1,parseInt(startDay),startHour,startMin,0,0);
                    var endDateObj = new Date(parseInt(endYear),parseInt(endMonth)-1,parseInt(endDay),endHour,endMin,0,0);
                    //alert(startDateObj+" "+sdate);
                    if(startDateObj>=sdate){
                        // add new event to the calendar
                        var ret=jfcalplugin.addAgendaItem(
                            "#mycal",
                            what,
                            startDateObj,
                            endDateObj,
                            false,{
                                fname: "Santa",
                                lname: "Claus",
                                leadReindeer: "Rudolph",
                                myDate: new Date(),
                                myNum: 42
                            },
                            {
                                backgroundColor: color_arr[event_status],
                                foregroundColor: $("#colorForeground").val()
                            });
                        if(ret)
                        {
                            $(this).dialog('close');
                        }
                    }
                    else{
                        alert("Sorry cn't create event, set time grater than current time 1 hours");
                    };
                }
            }/*,
             Cancel: function() {
             $(this).dialog('close');
             }*/
        },
        open: function(event, ui){
            // initialize start date picker
            jQuery("#startDate").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'yy-mm-dd'
            });
            // initialize end date picker
            jQuery("#endDate").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'yy-mm-dd'
            });
            // initialize with the date that was clicked
            jQuery("#startDate").val(clickDate);
            jQuery("#endDate").val(clickDate);
            // initialize color pickers

            jQuery("#colorSelectorBackground").ColorPicker({
                color: "#333333",
                onShow: function (colpkr) {
                    $(colpkr).css("z-index","10000");
                    $(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    $("#colorSelectorBackground div").css("backgroundColor", "#" + hex);
                    $("#colorBackground").val("#" + hex);
                }
            });
            //$("#colorBackground").val("#1040b0");
            jQuery("#colorSelectorForeground").ColorPicker({
                color: "#ffffff",
                onShow: function (colpkr) {
                    jQuery(colpkr).css("z-index","10000");
                    jQuery(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function (colpkr) {
                    jQuery(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    jQuery("#colorSelectorForeground div").css("backgroundColor", "#" + hex);
                    jQuery("#colorForeground").val("#" + hex);
                }
            });
            //$("#colorForeground").val("#ffffff");
            // put focus on first form input element
            jQuery("#what").focus();
        },
        close: function() {
            // reset form elements when we close so they are fresh when the dialog is opened again.
            jQuery("#startDate").datepicker("destroy");
            jQuery("#endDate").datepicker("destroy");
            jQuery("#startDate").val("");
            jQuery("#endDate").val("");
            jQuery("#startHour option:eq(0)").attr("selected", "selected");
            jQuery("#startMin option:eq(0)").attr("selected", "selected");
            jQuery("#startMeridiem option:eq(0)").attr("selected", "selected");
            jQuery("#endHour option:eq(0)").attr("selected", "selected");
            jQuery("#endMin option:eq(0)").attr("selected", "selected");
            jQuery("#endMeridiem option:eq(0)").attr("selected", "selected");
            jQuery("#what").val("");
            //$("#colorBackground").val("#1040b0");
            //$("#colorForeground").val("#ffffff");
        }
    });
    jQuery("#error").dialog({
        autoOpen: false,
        height: 400,
        width: 400,
        resizable: false,
        modal: true,Cancel: function() {
            $(this).dialog('close');
        }
    });
    /**
     * Initialize display event form.
     */
    jQuery("#display-event-form").dialog({
        autoOpen: false,
        width: 350,
        modal: true,
        resizable: false,
        buttons: {
            Cancel: function() {
                jQuery(this).dialog('close');
            },
            /*'Edit': function() {
             alert("Make your own edit screen or dialog!");
             },*/
            'Delete': function() {
                if(confirm("Are you sure you want to delete this agenda item?")){
                    if(clickAgendaItem != null){
                        jfcalplugin.deleteAgendaItemById("#mycal",clickAgendaItem.agendaId);
                        //jfcalplugin.deleteAgendaItemByDataAttr("#mycal","myNum",42);
                    }
                    jQuery(this).dialog('close');
                }
            }
        },
        open: function(event, ui){
            if(clickAgendaItem != null){
                var title = clickAgendaItem.title;
                var startDate = clickAgendaItem.startDate;
                var endDate = clickAgendaItem.endDate;
                var allDay = clickAgendaItem.allDay;
                var data = clickAgendaItem.data;

                var mnth = startDate.getMonth();
                var sday=startDate.getDate();
                var syear = startDate.getFullYear();
                var shr= startDate.getHours();
                var smin= startDate.getMinutes();
                var time_am="AM";
                if(shr>12){
                    shr-=12;
                    time_am="PM";
                }



                var emnth = endDate.getMonth();
                var eday=endDate.getDate();
                var eyear = endDate.getFullYear();
                var ehr= endDate.getHours();
                var emin= endDate.getMinutes();
                var etime_am="AM";
                if(ehr>12){
                    ehr-=12;
                    etime_am="PM";
                }
                var displayData="";

                displayData += "<div><div class='upr-box-tool'><h6 class='pull-left'><strong>Class Time</strong><br/><span class='fontsize3'>"+shr+":"+smin+" "+time_am+" to "+ehr+":"+emin+" "+etime_am+"</span></h6>";
                displayData +=	"<h6 class='pull-right right-text'><strong>Students</strong><br/><span class='fontsize3'>7/10</span></h6>";
                displayData +=	"</div>";
                displayData += "	<div class='bottom-box-tool'>";
                displayData += "<p>"+ title+"</p>";
                displayData += " </div></div>";


                // in our example add agenda modal form we put some fake data in the agenda data. we can retrieve it here.
                $("#display-event-form").append(displayData);

            }
        },
        close: function() {
            // clear agenda data
            jQuery("#display-event-form").html("");
        }
    });


});