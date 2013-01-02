<?php
    echo $this->Html->link( '<li'.($num%2==0 ? ' class="message-tm-active"' : null).' data-num="'.$num.'">
                                '.$this->Html->image($this->Layout->image($topic['Topic']['LastUser']['image_source'], 60, 60), array('alt'=>'User Image')).'
                                <h6 class="space15">'.$topic['Topic']['title'].'</h6>
                                <p>'.$topic['Topic']['LastPost']['content'].'</p>
                            </li>',
                            array('plugin'=>'forum','controller'=>'topics', 'action'=>'view', $topic['Topic']['slug']), array('escape'=>false));
?>