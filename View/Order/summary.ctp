<?php
$this->Html->scriptBlock('
$(document).ready(function() {
    mixpanel.track("Order. summary load");

    $(\'#orderNextButton\').click(function() {
        mixpanel.track("Order. Summary next click");

        $(\'#summaryForm\').submit();
    });

     $(\.upcoming-lesson-join\').click(function() {
            mixpanel.track("Order. Summary upcoming lesson join click");
    });
     $(\.upcoming-lesson-open\').click(function() {
        mixpanel.track("Order. Summary upcoming lesson open click");
    });
});
', array('inline'=>false));
$this->set('nextOrderStep', true);
$this->extend('/Order/Common/common');

$this->start('main');

echo $this->Form->create(false, array('url'=>array('controller'=>'Order', 'action'=>'prerequisites'), 'type'=>'post', 'id'=>'summaryForm'));
?>
<div class="pull-left fullwidth space12">
    <h2  class="pull-left"><strong><?php echo __('Summary');?></strong></h2>
    <!--<a href="order-billing-negotiate.html" class="btns btn-black pull-right black-negotiat">Negotiate</a>-->
</div>

<div class="order-frm-box order-frm-box1 pull-left">
    <ul class="billinginfo-order">
        <li>
            <label><?php echo __('Lesson Type');?></label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo Inflector::humanize($lesson_type); ?></p></div>
        </li>
        <li>
            <label><?php echo __('Lesson name');?></label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo $name; ?></p></div>
        </li>
        <li>
            <label><?php echo __('Lesson description');?> </label>
            <div class="order-billing-intext">
                <p><span>:</span> </p>
                <p class="order-billingtext"><?php echo $description; ?></p></div>
        </li>
        <li>
            <label><?php echo __('Datetime');?> </label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo $this->TimeTZ->nice($datetime); ?></p>
        </li>
        <li>
            <label><?php echo __('Duration');?> </label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo $duration_minutes; ?> min</p></div>
        </li>

        <li>
            <label><?php echo __('Price');?> </label>
            <div class="order-billing-intext"><p>
                <span>:</span><span class="space11"><?php
                        if($price) {
                            echo $price.'$';
                            if($price_actual_purchase) {
                                echo ' ('.sprintf(__('%1.2f$ short'), $price_actual_purchase).')';
                            }
                        } else {
                            echo __('Free');
                        }
                        ?></span>
                <?php if(isSet($max_students) && $max_students>1 && $full_group_student_price) {?>
                <p class="full-lesson"><label class="fulllesson">(<?php echo __('Full lesson discount price'); ?>:</label><span><?php echo $full_group_student_price; ?>$ <?php echo __('PER STUDENT'); ?>)</span></p>
                <?php } ?>
                </p>
            </div>
        </li>

        <?php if($lesson_type==LESSON_TYPE_LIVE  && isSet($max_students) && $max_students>1) {?>

        <li>
            <label><?php echo __('Max students');?></label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php
                if(isSet($num_of_students)) {
                    echo sprintf(__('%d of %d'), $num_of_students, $max_students);
                } else {
                    echo $max_students;
                }
                ?></p>
        </li>

        <?php
        }

        if($lesson_type==LESSON_TYPE_LIVE && $orderData['action']=='order') {
        ?>

        <li>
            <label><?php echo __('Public lesson');?> :</label>
            <div class="order-billing-intext"><p>
                <select name="is_public" id="4" class="large">
                    <option value="1"><?php echo __('Yes');?></option>
                    <option value="0"><?php echo __('No');?></option>
                </select>
            </p></div>
        </li>
        <?php
        }
        ?>
    </ul>
</div> <!-- /cpull-left space7 -->
<?php
echo $this->Form->end();
$this->end();
?>