<?php
/**
 * Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
if($isAddMode) {
    if($allowAddByAuth) {

            echo $this->CommentWidget->element('form', array('comment' => (!empty($comment) ? $comment : 0)));

    } else {
        echo sprintf(__d('comments', 'If you want to post comments, you need to login first.'),
            $this->Html->link(__('comments', 'login'), array('/login')));
    }
} else {
    ?>

<script type="text/javascript">
    //Form submit
    pfObj.loadForm('#comments form', '#comments', 'post', {});


    //Paginator
    $('.paging').undelegate('a', 'click');
    $('.paging').delegate('a', 'click', function(e) {
        var self = this;
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'html'

        }).done(function ( data ) {
                //Append data into form
                $('#comments').html(data);
            });

        return false;
    });

    //Cancel button
    $('.comments').undelegate('input.cancel', 'click');
    $('.comments').delegate('input.cancel', 'click', function(e) {
        e.preventDefault();
        $('#comment-form-container').remove();
        return false;
    });

    //Load comment form
    $('.comments').undelegate('a', 'click');
    $('.comments').delegate('a', 'click', function(e) {

        //Close other replay-post
        $('#comment-form-container').remove();


        //Load new replay using JS
        var self = this;
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'html'

        }).done(function ( data ) {
                //Append data into form
                $(self).closest('div.comment div').after(data);
            });

        return false;
    });
</script>

    <div class="comments">
        <a name="comments"></a>
        <?php

        //Add comment button
        if($allowAddByAuth) {
            echo '<div class="comment"><div>';
                echo $this->CommentWidget->link(__d('comments', 'Add comment'), am($url, array('comment' => 0)));
            echo '</div></div>';

        } else {
            echo sprintf(__d('comments', 'If you want to post comments, you need to login first.'),
                $this->Html->link(__('comments', 'login'), array('/login')));
        }


        echo $this->Tree->generate(${$viewComments}, array( 'callback' => array(&$this->CommentWidget, 'treeCallback'),
            'model' => 'Comment',
            'class' => 'tree-block space4'));


        echo '<div class="space4">';
            echo $this->CommentWidget->element('paginator');
        echo '</div>';
        ?>
    </div>
    <?php
    echo $this->Html->image('/comments/img/indicator.gif', array('id' => 'busy-indicator', 'style' => 'display:none;'));
}
