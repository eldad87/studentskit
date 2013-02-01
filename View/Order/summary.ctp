<?php
$this->Html->scriptBlock('
$(document).ready(function() {
    $(document).ready(function(){
        $(\'#orderNextButton\').click(function() {
            $(\'#summaryForm\').submit();
        });
    });
});
', array('inline'=>false));
$this->set('nextOrderStep', false);
$this->extend('/Order/Common/common');

$this->start('main');

echo $this->Form->create(false, array('url'=>array('controller'=>'Order', 'action'=>'prerequisites'), 'type'=>'post', 'id'=>'summaryForm'));
?>
<div class="pull-left fullwidth space12">
    <h2  class="pull-left"><strong>Summary</strong></h2>
    <!--<a href="order-billing-negotiate.html" class="btns btn-black pull-right black-negotiat">Negotiate</a>-->
</div>

<div class="order-frm-box order-frm-box1 pull-left">
    <ul class="billinginfo-order">
        <li>
            <label>Lesson Type</label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo Inflector::humanize($lesson_type); ?></p></div>
        </li>
        <li>
            <label>Lesson name</label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo $name; ?></p></div>
        </li>
        <li>
            <label>Lesson description </label>
            <div class="order-billing-intext">
                <p><span>:</span> </p>
                <p class="order-billingtext"><?php echo $description; ?></p></div>
        </li>
        <li>
            <label>Datetime </label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo $this->TimeTZ->nice($datetime); ?></p>
        </li>
        <li>
            <label>Duration </label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php echo $duration_minutes; ?> min</p></div>
        </li>

        <li>
            <label>Price </label>
            <div class="order-billing-intext"><p>
                <span>:</span><span class="space11"><?php echo $price ? $price.'$' : 'Free'; ?></span>
                <?php if(isSet($max_students) && $max_students>1 && $full_group_student_price) {?>
                <p class="full-lesson"><label class="fulllesson">(Full lesson discount price:</label><span><?php echo $full_group_student_price; ?>$ PER STUDENT)</span></p>
                <?php } ?>
                </p>
            </div>
        </li>

        <?php if($lesson_type==LESSON_TYPE_LIVE  && isSet($max_students) && $max_students>1) {?>

        <li>
            <label>Max students</label>
            <div class="order-billing-intext"><p><span>:</span></p><p class="order-billingtext"><?php
                if(isSet($num_of_students)) {
                    echo $num_of_students.' of ';
                }
                echo $max_students;
                ?></p>
        </li>

        <?php
        }

        if($lesson_type==LESSON_TYPE_LIVE && $orderData['action']=='order') {
        ?>

        <li>
            <label>Public lesson :</label>
            <div class="order-billing-intext"><p>
                <select name="is_public" id="4" class="large">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
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