<div id="comments">
    <?php
        $this->CommentWidget->options(array('allowAnonymousComment' => false,
                                            'target' => '#comments',
                                            'ajaxAction' => array('controller'=>'Lessons', 'action'=>'comments')));
        echo $this->CommentWidget->display(array('subtheme'=>'universito'));

        //echo $this->Js->writeBuffer(); // Write cached scripts
    ?>
</div>