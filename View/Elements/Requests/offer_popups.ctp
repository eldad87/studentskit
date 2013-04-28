<div id="makeOffer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="makeOffer" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><?php echo __('Offer a lesson'); ?></h3>
    </div>

    <?php echo $this->Form->create('UserLesson', array('class'=>'sk-form', 'url'=>array('controller'=>'Requests', 'action'=>'makeAnOffer'), 'method'=>'post', 'id'=>'offer-form')); ?>
    <input type="hidden" name="by" id="offer-by" /> <!-- Tell if to use datetime ot teacher_lesson_id -->
    <div class="modal-body">
    </div>

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
        <button class="btn btn-primary"><?php echo __('Send offer'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>