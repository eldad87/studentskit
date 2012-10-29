<?php
$this->Html->script('jquery.tipTip', array('inline'=>false));
$this->Html->css(array('tipTip'), null, array('inline'=>false));

$this->Html->scriptBlock('jQuery(document).ready(function($) {

		function createCalendarLessonToolTip(data) {
            var html = \'<div class="popupwindow radius3">\';
                html += \'<div class="upr-box-tool">\';
                html += \'	<h6 class="pull-left"><strong>Class Time</strong><br/><span class="fontsize3">\' + data.datetime + \'</span></h6>\';
                html += \'   	<h6 class="pull-right right-text"><strong>Students</strong><br/><span class="fontsize3">\' + data.num_of_students + \'/\' + data.max_students + \'</span></h6>\';
                html += \' </div>\';
                html += \' <div class="bottom-box-tool">\';
                html += \' 	    <p>\' + data.description + \'</p>\';

                if(data.num_of_students<data.max_students) {
                    html += \' 	    <p><a href="\' + data.url + \'">Lesson page</a></p>\';
                }
                html += \' </div>\';


            html += \'</div>\';

        return html;
        }

		$(".someClass").each(function() {
		    var json = $(this).attr(\'data-tooltip\');
		    var data = jQuery.parseJSON( json );

		    if(data.max_students) { //Only public item have this info
		        $(this).tipTip({maxWidth: "auto", edgeOffset: 10, keepAlive: true, content: createCalendarLessonToolTip(data)});
		    }
		});
	});
', array('inline'=>false));

$monthNames = Array("January", "February", "March", "April", "May", "June", "July",
    "August", "September", "October", "November", "December");


$prev_year = $year;
$next_year = $year;
$prev_month = $month-1;
$next_month = $month+1;

if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $year - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $year + 1;
}
?>
<div class="cal-all space8">
    <div class="heading-box fullwidth">
        <?php
        echo $this->Html->link(null, array($prev_year, $prev_month), array('class'=>'arrow-left pull-left arrws1'));
        ?>

        <div class="head-text pull-left">
            <h2><?php echo $monthNames[$month]; ?><!-- <span>(GMT +2)</span>--></h2>
            <h6 class="space3"><?php echo $year; ?></h6>
        </div>
        <?php
        echo $this->Html->link(null, array($next_year, $next_month), array('class'=>'arrow-right pull-left arrws1'));
        ?>
    </div>
    <div class="table-main">
        <table class="table-main2" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td>
                    <?php
                    $orderedMonthLessons = $this->Layout->lessonsToDaysInMonth($monthLessons, $month, $year);


                    //Get the biggest day of the week
                    $longestDay = 0;
                    $thisMonth = getdate (mktime(0,0,0,$month,1,$year));
                    $startDay = $thisMonth['wday'];
                    foreach($orderedMonthLessons AS $day=>$lessons) {
                        $count = count($lessons);
                        if($day<$startDay) {
                            $count++;
                        }
                        $longestDay = max($longestDay, $count);
                    }

                    foreach($orderedMonthLessons AS $day=>$orderedDaysLessons) {
                        echo $this->element('Order/calendar_day', array('day'=>$day, 'month'=>$month, 'year'=>$year, 'daysLessons'=>$orderedDaysLessons, 'longestDay'=>$longestDay));
                    }
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> <!-- /table-main -->
    <a href="#" class="space4 bluestrip">Continue</a>
    <ul class="colordetail">
        <li>
            <div class="colorbox colorbox-bgnd1"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail">
                    <span>Closed</span><br>
                    Full lesson which you can't join.
                </p>
            </div>
        </li>
        <li>
            <div class="colorbox colorbox-bgnd2"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail">
                    <span>Teacher approval</span><br>
                    Live lessons requests will wait for you approval on those dates.
                </p>
            </div>
        </li>
        <li>
            <div class="colorbox colorbox-bgnd3"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail">
                    <span>Open</span><br>
                    Lesson which you can join.
                </p>
            </div>
        </li>
        <li class="greenbox-margin">
            <div class="colorbox colorbox-bgnd4"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail">
                    <span>Auto approve</span><br>
                    New LIVE lessons requests will get auto-approve on those dates.
                </p>
            </div>
        </li>
    </ul>

</div> <!-- /cal-all -->