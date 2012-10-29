<?php
$days = array(  array('Sunday', 'Sun'), array('Monday', 'Mon'), array('Tuesday', 'Tue'),
                array('Wednesday', 'Wed'), array('Thursday', 'Thu'), array('Friday', 'Fri'),
                array('Saturday', 'Sat'),
);
?>
<table class="inner-tabel" cellpadding="0" cellspacing="0">
    <tbody>
    <tr class="table-heading">
        <td style="border-right:none;">
            <p><strong><?php echo $days[$day][0]; ?></strong></p><span class="sec-text pull-left"><strong><?php echo $days[$day][1]; ?></strong></span>
        </td>
    </tr>
    <tr class="table-heading2">
        <?php
        //Get the day this month stats on
        $thisMonth = getdate (mktime(0,0,0,$month,1,$year));
        $startDay = $thisMonth['wday'];



        $rows=0;
        if($day<$startDay) {
            $rows++;
            echo '<td></td>';
        }





        foreach($daysLessons AS $mDay=>$lessons) {
            //When you have 5 date-boxes on the same day (1, 8, 15, 22, 29 on sunday etc) - then you need to remove the boarder from the last box
            $class = (++$rows==$longestDay ? ' class="border-none1"' : null);



            //Print box start
            /*if($mDay==20) {
                echo '
            <td id="para20"',$class,'>
                <p>',$mDay,'</p>
                                                        <div class="time-tooltip-box">
                                                        	<div class="popupwindow radius3 time-tooltip">
                                                            <span class="arrow-img"></span>
                                                            <div class="upr-box-tool">
                                                                <h6 class="pull-left"><strong>Class Time</strong><br/><span class="fontsize3">9am to 10 am</span></h6>

                                                             </div>
                                                             <div class="bottom-box-tool sk-form">
                                                                <form>
                                                                	<label class="calnder-labeltime">Time :</label>
                                                                    <input type="text" value="16:16" class="calander-time">
                                                                    <button class="btn-black time-submit">Submit</button>
                                                                </form>
                                                             </div>
                                                        </div>
                                                      </div>

                <table class="table-heading3">
                    <tbody>
                        <tr>';
            } else {*/
            echo '
            <td',$class,'>
                <p>',$mDay,'</p>
                <table class="table-heading3">
                    <tbody>
                        <tr>';
            //}


            //Print lessons rows
            foreach($lessons AS $lesson) {
                $colorClass = 'black';

                if(isSet($lesson['max_students'])) { //Public lessons
                    if($lesson['max_students']>$lesson['num_of_students']) {
                        $colorClass = 'oreng';
                    }
                }

                if(!$lesson['name']) {
                    $lesson['name'] = __('blocked');
                } else {
                    //Remove \n \r \t from description
                    $lesson['description'] = str_replace(array("\n", "\r", "\t"), array('', '', ''), $lesson['description']);

                    //Create lesson url
                    $lesson['url'] = Router::url(array('controller'=>'Home', 'action'=>'teacherLesson', $lesson['teacher_lesson_id']), true);

                    //Convert datetime + duration to fromX toY -> 9am to 11am
                    $lesson['datetime'] = strtotime($lesson['datetime']);
                    $lesson['datetime'] = date('ga', $lesson['datetime']).' '.__('to').' '.date('ga', $lesson['datetime']+$lesson['duration_minutes']*MINUTE);
                }


                echo '<td class="color-box"><a class="'.$colorClass.' someClass" href="#" data-tooltip=\''.json_encode($lesson).'\'>'.String::truncate($lesson['name'], 10, array('ending'=>'..')).'</a></td>';
            }


            //Print box end
            echo '
                        </tr>
                    </tbody>
                </table>
            </td>';
        }

        if($rows<$longestDay) {
            echo '<td class="border-none1"></td>';
        }
        ?>
        <!--<td><p>01</p></td>
        <td><p>08</p>
            <table class="table-heading3">
                <tbody>
                <tr>
                    <td class="color-box"><a class="black someClass" href="#"></a></td>
                </tr>
                </tbody>
            </table>
        </td>
        <td><p>15</p>
            <table class="table-heading3">
                <tbody>
                <tr>
                    <td class="color-box"><a class="black someClass" href="#"></a></td>
                    <td class="color-box"><a class="black someClass" href="#"></a></td>
                </tr>
                </tbody>
            </table>
        </td>
        <td><p>22</p></td>
        <td class="border-none1"><p>29</p></td>-->
    </tr>
    </tbody>
</table>