<script type="text/javascript">
    $(document).ready(function(){
        initNextButton();
    });
</script>
<div class="cont-span6 cbox-space">
    <h3 class="space21"><?php echo __('Congratulation!'); ?></h3>
    <p class="space21"><?php echo __('You\'ve finished creating a subject. As a step of precaution; please review your content again, only then click on the FINISH button.'); ?></p>

    <?php
    if( $creationStage && $creationStage < CREATION_STAGE_PUBLISH ) {
        echo $this->Html->link(__('FINISH'), '#', array( 'class'=>'btn-blue pull-right nextButton',
                'data-creation-stage'=>CREATION_STAGE_PUBLISH,
                'data-subject-id'=>$subjectId
            )
        );
    }
    ?>
</div>