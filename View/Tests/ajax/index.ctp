<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
    $(document).ready(function(){
        initTabs(false);
        initNextButton();
    });
</script>
<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-delete',
    'title'=>__('Delete a test'),
    'description'=>__('This procedure may be irreversible.
                       Do you want to proceed?'),
    'cancelUrl'=>array('controller'=>'Tests', 'action'=>'delete', '{id}')));
?>
<div class="cont-span6 cbox-space">
    <div class="fullwidth pull-left">
        <h5 class=" pull-left space2"><strong>Tests</strong></h5><div class="clear"></div>

        <ul class="test-list">
            <?php
                foreach($response['response']['results']['tests'] AS $test) {
            ?>
                    <li id="quiz_<?php echo $test['test_id']; ?>">
                        <span>
                            <strong><?php echo $test['name']; ?></strong> - <?php echo $test['description']; ?>
                        </span>
                        <div class="pull-right">

                            <?php
                            echo $this->Html->link( __('Start').'<i class="iconSmall-add-arrow"></i>',
                                '#',
                                array( 'class'=>'btn-blue text-color pull-left load3 space25',
                                    'rel'=>Router::url(
                                        array('controller'=>'Tests', 'action'=>'take', $test['test_id'])
                                    ),
                                    'escape'=>false
                                )
                            );
                            ?>

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

                            <a class="pull-left confirm-delete" href="#"  data-cancel-prefix="quiz" data-id="<?php echo $test['test_id']; ?>">
                                <i class="iconSmall-red-cross redcross actionButton"></i>
                            </a>
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