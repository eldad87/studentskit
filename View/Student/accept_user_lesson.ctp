<?php
if(isSet($paymentPage)) {
    echo '<p><strong>'.__('In order for those changes to take effect, please continue through the ').'</strong>'.$this->Html->link(__('Order page'), $paymentPage).'</p>';
} else {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php
                if(isSet($success)) {
                    echo '$(\'#accept-popup\').modal(\'hide\');';
                } else if(isSet($error)) {
                    echo 'showError(\'#accept-popup .modal-body\',\''.__('Internal Error').'\', \'\')';
                }

                if(isSet($moveElement) && isSet($moveToElement)) {
                    echo '$(\''.$moveElement.'\').appendTo(\''.$moveToElement.'\');';
                }

                if(isSet($removeElement)) {
                    echo '$(\''.$removeElement.'\').hide();';
                }
            ?>
        });
    </script>
    <?php
        $this->Form->create('UserLesson');
    ?>
        <fieldset>
            <div class="booking-nagotiat">
                <h6 class="pull-left fullwidth head-textcolor"><?php echo  ($userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_STUDENT ||
                                                                            $userLessonData['stage']==USER_LESSON_RESCHEDULED_BY_TEACHER) ?
                                                                                __('Modified Settings') : __('Original Settings')
                    ?></h6>
                <ul>
                    <li>
                        <label><!--<i class="iconSmall-info space10"></i>--><?php echo __('Datetime'); ?>:</label>
                        <div class="negote-inputbox space3"><?php echo $userLessonData['datetime']; ?></div>
                    </li>
                    <li>
                        <label><?php echo __('Duration'); ?>:</label>
                        <div class="negote-inputbox space3"><?php echo $userLessonData['duration_minutes']; ?></div>
                    </li>
                    <li>
                        <label><?php echo __('1 on 1 price'); ?>:</label>
                        <div class="negote-inputbox space3"><?php echo $userLessonData['1_on_1_price'] ? $userLessonData['1_on_1_price'] : __('Free'); ?></div>
                    </li>

                    <?php if($userLessonData['lesson_type']==LESSON_TYPE_LIVE ) { ?>
                      <li>
                            <label><?php echo __('Max students'); ?>:</label>
                            <div class="negote-inputbox space3"><?php echo $userLessonData['max_students']; ?></div>
                        </li>
                        <?php if($userLessonData['1_on_1_price'] && $userLessonData['max_students']>1 ) { ?>
                            <li>
                                <label><?php echo __('Full group student price'); ?>:</label>
                                <div class="negote-inputbox space3"><?php echo $userLessonData['full_group_student_price']; ?></div>
                            </li>
                        <?php } ?>
                    <?php } ?>

                    <li>
                        <label><?php echo __('Is public'); ?>:</label>
                        <div class="negote-inputbox space3"><?php echo $userLessonData['is_public'] ? __('Yes') : __('No'); ?></div>
                    </li>
                </ul>
            </div>
        </fieldset>
    <?php
    $this->Form->end();
}
?>