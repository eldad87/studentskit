<script type="text/javascript">
    $(document).ready(function(){
        initTabs(false);
        initNextButton();
    });
</script>
<div class="cont-span6 cbox-space">
    <div class="fullwidth pull-left">
        <h5 class=" pull-left space2"><strong>Tests</strong></h5><div class="clear"></div>

        <ul class="test-list">
            <?php
                foreach($response['response']['results']['tests'] AS $test) {
            ?>
                    <li>
                        <strong><?php echo $test['name']; ?></strong> - <?php echo $test['description']; ?>
                        <div class="pull-right">
                            <?php
                                echo $this->Html->link('<i class="iconSmall-pencil pencilicon actionButton""></i>',
                                                        '#',
                                                        array( 'class'=>'pull-left load3',
                                                            'rel'=>Router::url(
                                                                array('controller'=>'Tests', 'action'=>'manage', $response['response']['results']['subject_id'], $test['test_id'])
                                                            ),
                                                            'escape'=>false
                                                        )
                                );
                            ?>
                            <i class="iconSmall-red-cross redcross actionButton" id="delete_{file_system_id}"></i>
                        </div>
                    </li>
            <?php
                }
            ?>
        </ul>


        <div class="cbox-space">
            <?php
                echo $this->Html->link(__('Create'), '#', array( 'class'=>'btn-blue pull-left load3',
                    'rel'=>Router::url(array('controller'=>'Tests', 'action'=>'manage', $response['response']['results']['subject_id']))));
            ?>




            <?php
            if( $creationStage && $creationStage < CREATION_STAGE_TESTS ) {
                echo $this->Html->link(__('Next'), '#', array( 'class'=>'btn-blue pull-right nextButton',
                                                            'data-creation-stage'=>CREATION_STAGE_TESTS,
                                                            'data-subject-id'=>$subjectId
                                                        )
                );
            }
            ?>
        </div>
    </div> <!-- /fullwidth -->
</div>