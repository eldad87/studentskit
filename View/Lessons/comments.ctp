
<?php
    $this->CommentWidget->options(array('allowAnonymousComment' => false,
                                        'target' => '#comments',
                                        'ajaxAction' => array('controller'=>'Lessons', 'action'=>'comments'),
                                        'cancelOptions'=>array('class'=>'btn btn-primary pull-right')));

    echo $this->element('Comments.ajax', array('displayOptions'=>array( 'subtheme'=>'universito') ));

    //echo $this->Js->writeBuffer(); // Write cached scripts
?>