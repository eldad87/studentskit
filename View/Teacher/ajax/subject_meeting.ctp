<?php
    //echo $this->element('Teacher\webinar_popup', array('buttonSelector'=>'.webinar'));
?>
<script type="text/javascript">
    $(document).ready(function(){
        initNextButton();
    });
</script>

<div class="fullwidth pull-left">
    <div class="cont-span6 cbox-space">

            <p class="pull-left clear-left fontsize1"><?php echo __('Take your time and create your webinar'); ?></p>
            <p class="pull-left clear-left fontsize1"><?php echo __('Please note, any change that will be made here, will apply to your future lessons'); ?></p>


            <p class="pull-left clear-left fontsize1">
            <?php
                echo $this->Html->link('Launch', array('controller'=>'Lessons', 'action'=>'subject', $subjectId), array('class'=>'webinar', 'target'=>'_blank'));
            ?>
            </p>
    </div>
<?php
    if(isSet($creationStage) && $creationStage) {
?>
    <div class="cont-span6 cbox-space">
            <div class="control-group control2">
                <label class="control-label"></label>
                <div class="control">
                    <button class="btn-blue pull-right nextButton" data-creation-stage="<?php echo CREATION_STAGE_MEETING; ?>" data-subject-id="<?php echo $subjectId; ?>" type="Save"><?php echo __('Next'); ?></button>
                </div>
            </div>
    </div>
<?php
    }
?>
</div>
