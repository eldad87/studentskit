<?php
    $this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Home. index load");

        $(\'.subject-box\').click(function() {
            mixpanel.track("Home. Index subject click");
        });
        $(\'.lesson-request-popup\').click(function() {
            mixpanel.track("Home. Index lesson request click");
        });
        $(\#search_form\').submit(function() {
            mixpanel.track("Home. Index search submit");
        });
    });
    ', array('inline'=>false));

    echo $this->element('Home'.DS.'search');
?>

<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span8">
                <h2 class="pull-left"><i class="iconBig-about space1"></i><?php echo __('Newest'); ?></h2>
                <?php
                    echo $this->Layout->subjectRequestPopupButton();
                    echo $this->element('Home'.DS.'subject_request_popup');
                ?>
                <ul class="row">
                    <?php
                    if($newSubjects) {
                        foreach($newSubjects AS $newSubject) {
                            $newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
                            echo '<li class="cont-span4 spc space2">';

                            echo $this->Html->link( $this->element( 'subject', array(
                                                                    'subjectId'             =>$newSubject['Subject']['subject_id'],
                                                                    'teacherUserId'         =>$newSubject['Subject']['user_id'],
                                                                    'teacherUsername'       =>$newSubject['Teacher']['username'],
                                                                    'name'                  =>$newSubject['Subject']['name'],
                                                                    'description'           =>$newSubject['Subject']['description'],
                                                                    'avarageRating'         =>$newSubject['Subject']['avarage_rating'],
                                                                    'oneOnOnePrice'         =>$newSubject['Subject']['1_on_1_price'],
                                                                    'fullGroupStudentPrice' =>$newSubject['Subject']['full_group_student_price'],
                                                                    'imageSource'           =>$newSubject['Subject']['image_source'],
                                                                    'lessonType'            =>$newSubject['Subject']['lesson_type'],
                                                                    'tooltipData'           =>$newSubject['Subject'],
                            )), array('controller'=>'Home', 'action'=>'teacherSubject', $newSubject['Subject']['subject_id']), array('escape'=>false, 'class'=>'subject-box'));
                            echo '</li>';
                        }
                    }
                    ?>
                </ul> <!-- /row -->
                <div class="color-btn-row">
                    <div class="color-btn-span">
                        <a href="#" title="" class="btn-color-green">
                            <i class="iconMedium-work"></i>
                            <p class="btn-color-txt"><?php echo sprintf(__('%s How it %s WORKS?'), '<span>', '</span><br />'); ?></p>
                        </a>
                    </div>
                    <div class="color-btn-span">
                        <a href="#" title="" class="btn-color-red">
                            <i class="iconMedium-more"></i>
                            <p class="btn-color-txt"><?php echo sprintf(__('%s Learn %s MORE'), '<span>', '</span><br />'); ?></p>
                        </a>
                    </div>
                    <div class="color-btn-span last">
                        <a href="#" title="" class="btn-color-blue">
                            <i class="iconMedium-teacher"></i>
                            <p class="btn-color-txt"><?php echo sprintf(__('%s Become A %s TEACHER'), '<span>', '</span><br />'); ?></p>
                        </a>
                    </div>
                </div>
            </div> <!-- /cont-span8 -->
            <div class="cont-span4 spc-mar">
                <h2><i class="iconBig-mesg space1"></i><?php echo __('Community Messages'); ?></h2>
                <div class="board-msg-container">
                    <ul class="board-msg">
                        <?php
                        if($latestTopics) {
                            $bgColor=0;
                            foreach($latestTopics AS $latestTopic) {
                                echo $this->element('Home'.DS.'last_board_msg_li', array('latestTopic'=>$latestTopic, 'bgColor'=>++$bgColor));
                                if($bgColor==2) {
                                    $bgColor=0;
                                }
                            }
                        } else {
                            echo '<li class="bg-color1">
                                <p>',__('No community messages yet'),'</p>
                            </li>';
                        }
                    ?>
                    </ul>
                </div>
                <?php
                    if(count($latestTopics)>=$latestTopicsCount) {
                        echo '<a class="more-btn1">',_('More Threads'),'</a>';
                    }
                ?>

            </div> <!-- /cont-span8 -->
        </div>
    </div>
</Section>