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
?>


<div id="comment-form-container">
    <div class="cbox-space">
        <div class="fullwidth pull-left space7">
            <?php

            $_url = array_merge($url, array('action' => str_replace(Configure::read('Routing.admin') . '_', '', $this->request->action)));
            foreach (array('page', 'order', 'sort', 'direction') as $named) {
                if (isset($this->passedArgs[$named])) {
                    $_url[$named] = $this->passedArgs[$named];
                }
            }
            if ($target) {
                $_url['action'] = str_replace(Configure::read('Routing.admin') . '_', '', 'comments');
                $ajaxUrl = $this->CommentWidget->prepareUrl(array_merge($_url, array('comment' => $comment, '#' => 'comment' . $comment)));
                echo $this->Form->create(null, array('url' => $ajaxUrl, 'target' => $target, 'class'=>'sk-form'));
            } else {
                echo $this->Form->create(null, array('class'=>'sk-form', 'url' => array_merge($_url, array('comment' => $comment, '#' => 'comment' . $comment))));
            }
            echo '<fieldset>';

                //echo $this->Form->input('Comment.title', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2'))));
                echo $this->Form->input('Comment.body',
                    $this->Layout->styleForInput(array('error' => array('body_required' => __d('comments', 'This field cannot be left blank'),
                                                                        'body_markup' => sprintf(__d('comments', 'You can use only headings from %s to %s' ), 4, 7)),
                                                        'div'=>array('class'=>'control-group control2'),
                                                        'label'=>false))
                );
                // Bots will very likely fill this fields
                echo $this->Form->input('Other.title', array('type' => 'hidden'));
                echo $this->Form->input('Other.comment', array('type' => 'hidden'));
                echo $this->Form->input('Other.submit', array('type' => 'hidden'));

                if ($target) {
                    echo $this->Js->submit(__d('comments', 'Submit'), array_merge(array('url' => $ajaxUrl), $this->CommentWidget->globalParams['cancelOptions']));
                    echo $this->Js->submit(__d('comments', 'Cancel'), array('class'=>'btn pull-right space10 cancel'));
                } else {
                    echo $this->Form->submit(__d('comments', 'Submit'));
                }

            echo '</fieldset>';
            echo $this->Form->end();
            ?>
        </div>
    </div>
</div>