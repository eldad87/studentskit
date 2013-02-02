<!-- board-topics start -->
<div class="lesson-box pad8 space4">
    <h3 class="radius1"><strong><?php echo __('Latest board messages'); ?></strong></h3>
    <div class="box-subject2 radius3 a-black">
        <?php
            $latestPostsCount = count($latestPosts);
            foreach($latestPosts AS $latestPost) {
                echo $this->Html->link( '<div class="main-student'.(--$latestPostsCount ? ' bod2' : null).'">
                                            <div class="right-student-box latestform-msg right-student-box1">
                                                <div ><h6 class="pull-left space10"><strong>'.$latestPost['Topic']['title'].'</strong></h6><em class="fontsize1">('.$latestPost['Post']['modified'].')</em></div>
                                                <p>'.$latestPost['Post']['content'].'</p>
                                            </div>
                                        </div>',
                                        array('plugin'=>'forum','controller'=>'topics', 'action'=>'view', $latestPost['Topic']['slug']), array('escape'=>false));
            }

            if(!$latestPosts) {
                echo '<p>',__('No community messages'),'</p>';
            }
        ?>
    </div>
</div>
<!-- board-topics ends -->