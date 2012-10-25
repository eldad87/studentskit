<?php echo $this->element('Home/search');  ?>


<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span8">
                <h2 class="pull-left"><i class="iconBig-about space1"></i>Newest subjects</h2>
                <button type="button" class="btns btn-black pull-right"><?php echo $this->Html->link('Lesson Requests', array('controller'=>'Requests')); ?></button>
                <ul class="row">
                    <?php
                    if($newSubjects) {
                        foreach($newSubjects AS $newSubject) {
                            $newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
                            echo '<li class="cont-span4 spc space2">';

                            echo $this->Html->link( $this->element('subject', array(
                                                                    'subjectId'             =>$newSubject['Subject']['subject_id'],
                                                                    'teacherUserId'         =>$newSubject['Subject']['user_id'],
                                                                    'teacherUsername'       =>$newSubject['Teacher']['username'],
                                                                    'name'                  =>$newSubject['Subject']['name'],
                                                                    'description'           =>$newSubject['Subject']['description'],
                                                                    'avarageRating'         =>$newSubject['Subject']['avarage_rating'],
                                                                    'oneOnOnePrice'         =>$newSubject['Subject']['1_on_1_price'],
                                                                    'fullGroupStudentPrice' =>$newSubject['Subject']['full_group_student_price'],
                                                                    'imageSource'           =>$newSubject['Subject']['image_source'],
                            )), array('controller'=>'Home', 'action'=>'teacherSubject', $newSubject['Subject']['subject_id']), array('escape'=>false));
                            echo '</li>';
                        }
                    }
                    ?>
                </ul> <!-- /row -->
                <div class="color-btn-row">
                    <div class="color-btn-span">
                        <a href="#" title="" class="btn-color-green">
                            <i class="iconMedium-work"></i>
                            <p class="btn-color-txt"><span>How it</span><br>Works?</p>
                        </a>
                    </div>
                    <div class="color-btn-span">
                        <a href="#" title="" class="btn-color-red">
                            <i class="iconMedium-more"></i>
                            <p class="btn-color-txt"><span>Learn</span><br>More</p>
                        </a>
                    </div>
                    <div class="color-btn-span last">
                        <a href="#" title="" class="btn-color-blue">
                            <i class="iconMedium-teacher"></i>
                            <p class="btn-color-txt"><span>Become A</span><br>Teacher</p>
                        </a>
                    </div>
                </div>
            </div> <!-- /cont-span8 -->
            <div class="cont-span4 spc-mar">
                <h2><i class="iconBig-mesg space1"></i>Board Messages</h2>
                <ul class="board-msg">
                    <?php
                    if($latestTopics) {
                        $i=0;
                        foreach($latestTopics AS $latestTopic) {
                            echo $this->Html->link(
                                    '<li class="bg-color'.(++$i).'">
                                        <div class="user-pic1">'.$this->Html->image($this->Layout->image($latestTopic['User']['image_source'], 60, 60),
                                                                                    array('alt' => 'User image')).'</div>
                                        <div class="usr-text1">
                                            <h6>'.$latestTopic['Topic']['title'].'</h6>
                                            <p>'.$latestTopic['LastPost']['content'].'</p>
                                        </div>
                                    </li>', array('plugin'=>'forum','controller'=>'topics', 'action'=>'view', $latestTopic['Topic']['slug']), array('escape'=>false));
                            if($i==2) {
                                $i=0;
                            }
                        }
                    }
                ?>
                </ul>
                <a class="more-btn1">More Threads</a>
            </div> <!-- /cont-span8 -->
        </div>
    </div>
</Section>