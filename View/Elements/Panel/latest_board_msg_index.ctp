<h5 class="space2"><strong><?php echo __('Board Messages'); ?></strong></h5>
<div class="pull-left space8 fullwidth">

    <ul class="message-tm-stu a-black">

<?php
$i = 0;
foreach($latestUpdatedTopics AS $topic) {
    $i++;

    echo $this->Html->link( '<li'.($i%2==0 ? ' class="message-tm-active"' : null).'>
                                '.$this->Html->image($this->Layout->image($topic['Topic']['LastUser']['image_source'], 60, 60), array('alt'=>'User Image')).'
                                <h6 class="space15">'.$topic['Topic']['title'].'</h6>
                                <p>'.$topic['Topic']['LastPost']['content'].'</p>
                            </li>',
                            array('plugin'=>'forum','controller'=>'topics', 'action'=>'view', $topic['Topic']['slug']), array('escape'=>false));
}
?>
            </ul>
            <a href="#" class="message-tm-more fontsize1">More Threads</a>
        </div>