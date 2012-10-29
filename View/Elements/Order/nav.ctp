<?php
$step = 1;
$actionName = strtolower($this->request->params['action']);

$calendar = ($orderData['action']=='order' && $orderData['lesson_type']==LESSON_TYPE_LIVE) ? true : false;
$payment = $orderData['price'] ? true : false;

//On status, there is no need to show steps
if($actionName!='status' && $actionName!='paidStatus'
    //there is no need to show the nav steps if there is only one (summary)
    && ($calendar || $payment)) {

?>
<div class="inner-box-step radius2 border1">
    <ul class="step-box">
        <?php
            if($calendar) {
                echo '<li>';
                echo $this->Html->link('<h5>Step '.$step++.'</h5><p class="fontsize1 centered ">Select Lesson Time</p>',
                                        array('controller'=>'Order', 'action'=>'init', 'order', $orderData['id']),
                                        array('escape'=>false, 'class'=>($actionName=='calendar' ? 'active4' : false)));
                echo '</li>';
            }
        ?>
        <li>
            <a href="#" <?php echo $actionName=='summary' ? 'class="active4" ' : null; ?>><h5>Step <?php echo $step++; ?></h5><p class="fontsize1 centered ">Summary</p></a>
        </li>

        <?php if($payment) { ?>
        <li>
            <a href="#"><h5>Step <?php echo $step++; ?></h5><p class="fontsize1 centered ">Payment</p></a>
        </li>
        <?php } ?>

    </ul>
</div>

<?php
}
?>