<script type="text/javascript">


    $(document).ready(function() {
        $('#quiz').quizRun({
            questionsContainer: '.question-list',
            questionsSelector: '.question-block',
            nextSelector: '.next-question'
        });

        initTabs(false);
    });

</script>
<?php
/*echo $this->Html->link('reload',
    '#',
    array( 'class'=>'pull-left load3',
        'rel'=>Router::url(array('controller'=>'Tests', 'action'=>'take', $response['response']['results']['test_id']))));*/
?>
<div class="cont-span19 cbox-space">
    <form class="sk-form">
        <fieldset id="quiz">
            <ol class="testform question-list">

                <?php
                foreach($response['response']['results']['questions'] AS $qKey=>$question) {
                    ?>
                    <li class="question-block">
                        <h4 class="pull-left"><?php echo ($qKey+1),'. ',$question['q'] ?>. </h4>
                        <div class="pull-left fullwidth space11">
                            <ul class="space30">
                                <?php
                                    foreach($question['a'] AS $aKey=>$answer) {
                                        echo '<li><input type="radio" name="answer_',$qKey,'" value="',$aKey,'" data-ra="',
                                        ($aKey==$question['ra'] ? 'true' : 'false')
                                        ,'" /> <p class="test-option">',$answer,'</p></li>';
                                    }
                                ?>
                            </ul>
                        </div>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <div class="control-group">
                        <button class="btn-blue pull-right next-question" type="button">Next <i class="iconSmall-add-arrow"></i></button>
                    </div>
                </li>
            </ol>

            <div class="score space32">
                <h3 class="pull-left space2">You have answered correctly on <span class="rightAnswerCount">10</span> out of <span class="questionsCount">14</span> questions!</h3>

                <div class="control-group">
                    <?php
                    echo $this->Html->link('Done',
                        '#',
                        array( 'class'=>'pull-right btn-blue load3',
                            'rel'=>Router::url(array('controller'=>'Tests', 'action'=>'index', $response['response']['results']['subject_id']))));
                    ?>
                </div>
            </div>

        </fieldset>


    </form>
</div>