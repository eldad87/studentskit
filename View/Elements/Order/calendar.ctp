<?php
$isTeacher = (isSet($isTeacher) ? $isTeacher : false);
$inline = (isSet($inline) ? $inline : false);

//CSS
echo $this->Html->css(array('jquery-ui/smoothness/jquery-ui', 'frontierCalendar/jquery-frontier-cal-1.3.2', 'tipTip'), null, array('inline'=>$inline));

//Add JS tp run the calendar
echo $this->Html->scriptBlock('
    var months = [  \''.__('January').'\', \''.__('February').'\', \''.__('March').'\', \''.__('April').'\',
                    \''.__('May').'\', \''.__('June').'\', \''.__('July').'\', \''.__('August').'\',
                    \''.__('September').'\', \''.__('October').'\', \''.__('November').'\', \''.__('December').'\'];

    var calendarSelector = \'#mycal\';
    var colors = {openUserLessons: \'#07B939\', openTeacherLesson: \'#FF8C1C\', closed: \'#333333\'};
', array('inline'=>$inline));

//JS
?>
<script type="text/javascript">
    autoJSLoaderObj.tryLoadChain('Hashtable', ['/js/lib/jshashtable-2.1.js']);
    autoJSLoaderObj.tryLoadChain('$.fn.tipTip', ['/js/jquery.tipTip.js']);
    autoJSLoaderObj.tryLoadChain('$.fn.qtip', ['/js/jquery-qtip-1.0.0-rc3140944/jquery.qtip-1.0-custom.js']);
    autoJSLoaderObj.tryLoadChain('$.fn.jFrontierCal', ['/js/frontierCalendar/jquery-frontier-cal-1.3.2-custom.js']);
    autoJSLoaderObj.tryLoadChain('NoneExistingObject', ['/js/calender.js'], onCalenderLoad);

    function onCalenderLoad() {
        <?php
            //Add agenda if any
            $addAgendaJS = '';
            foreach($allLiveLessons AS $lLesson) {
                if(isSet($lLesson['name'])) {
                    $addAgendaJS .= "
                        addAgenda(  '{$lLesson['teacher_lesson_id']}',
                                    '{$lLesson['name']}',
                                    '{$lLesson['description']}',
                                    '{$lLesson['num_of_students']}',
                                    '{$lLesson['max_students']}',
                                    '{$lLesson['datetime']}',
                                    '{$lLesson['duration_minutes']}',
                                    '{$this->Layout->image($lLesson['image_source'], 38, 38)}',
                                    '{$lLesson['type']}'
                                    );";
                } else {
                    $addAgendaJS .= "
                    addBlockedAgenda('  {$lLesson['datetime']}',
                                        {$lLesson['duration_minutes']});";
                }
            }
            echo $addAgendaJS;
        ?>
    }
</script>

<!-- Tooltip template -->
<div id="tooltip-template" class="hide">
    <div class='popupwindow radius3'>
        <div class='upr-box-tool'>
            <h6 class='pull-left'>
                <strong>Class Time</strong><br/>
                <span class='fontsize3'>{startDate}, {durationMin} Minutes</span>
            </h6>
            <h6 class='pull-right right-text'>
                <strong>Students</strong><br/>
                <span class='fontsize3'>{currentStudentsCount}/{maxStudentsCount}</span>
            </h6>
        </div>
        <div class='bottom-box-tool'>
            {image}
            <p class="pull-left">
                <strong>{title}</strong>
                <br />
                {description}
            </p>
        </div>
    </div>
</div>


<div class="cal-all space8">
    <div class="heading-box fullwidth space14">
        <!--<a href="#" class="arrow-left pull-left arrws1" ></a>-->
        <div id="BtnPreviousMonth" class="pull-left"><a href="#" class="arrow-left pull-left arrws1" ></a></div>
        <div class="head-text pull-left">
            <h2 class="pull-left fullwidth">
                <input type="text" id="monthDisplay" class="monthname-box" disabled="disabled" />
                <input type="text" id="yearDisplay" class="yearname-box" disabled="disabled" />
            </h2>
        </div>
        <!-- <a href="#" class="arrow-right pull-left arrws1"></a>  -->
        <div id="BtnNextMonth" class="pull-right">
            <a href="#" class="arrow-right pull-right arrws1"></a>
        </div>
    </div>
    <div class="fullwidth pull-left" id="calendar-msg"></div>
    <div id="mycal"></div>
    <!-- debugging-->
    <div id="calDebug"></div>


    <div id="add-event-form" title="Class Time">
        <form action="<?php echo Router::url(array('controller'=>'order', 'action'=>'setLessonDatetime')) ?>" method="post" id="setLessonDatetime">
            <input class="hide" id="startYear" type="text" name="data[UserLesson][datetime][year]" id="year" value="" />
            <input class="hide" id="startMonth" type="text" name="data[UserLesson][datetime][month]" id="month" value="" />
            <input class="hide" id="startDay" type="text" name="data[UserLesson][datetime][day]" id="day" value="" />

            <fieldset>
                <table class="fullwidth">
                    <tr>

                        <td>
                            <label>Start Hour</label>
                            <select id="startHour" class="text ui-widget-content ui-corner-all scheduleTime" name="data[UserLesson][datetime][hour]">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        <td>
                        <td>
                            <label>Start Minute</label>
                            <select id="startMin" class="text ui-widget-content ui-corner-all scheduleTime" name="data[UserLesson][datetime][min]">
                                <option value="00">00</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                            </select>
                        <td>
                        <td>
                            <label>Start AM/PM</label>
                            <select id="startMeridiem" class="text ui-widget-content ui-corner-all scheduleTime" name="data[UserLesson][datetime][meridian]">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>

    </div>
    <ul class="colordetail">
        <li>
            <div class="colorbox colorbox-bgnd1"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail"> <span><?php echo __('Closed'); ?></span><br>
                <?php echo __('None public or Full lesson.'); ?>
                </p>
            </div>
        </li>
        <li>
            <div class="colorbox colorbox-bgnd3"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail"> <span><?php echo __('Open'); ?></span><br>
                    <?php echo __('Public and Available lessons.'); ?>
                </p>
            </div>
        </li>
        <?php
        if($isTeacher) {
        ?>
        <li>
            <div class="colorbox colorbox-bgnd4"></div>
            <div class="colordetiail-bar">
                <p class="colorcode-detail"> <span><?php echo __('Optional lessons'); ?></span><br>
                    <?php echo __('Lessons that you may take participation in, includig pending invitations and under negotiation lessons.'); ?>
                </p>
            </div>
        </li>
        <?php
        }
        ?>
    </ul>

    <span id="error"><?php
        //WTF this is for?
        echo __('Please select a future dae'); ?></span>
    <div id="display-event-form" title="View Agenda Item"> </div>

</div>
<!-- /cal-all -->