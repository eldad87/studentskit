<!-- Container
================================================== -->
<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">
                <div class="cont-span15 c-box-mar">
                    <div class="lesson3-box c-mar-none radius2">
                        <div class="order-math btn-black radius1">
                            <i class="iconBig-liner-icon pull-left space1"></i>
                            <h2 class="pull-left"><?php echo (isSet($subjectData['name']) ? $subjectData['name'] : $name); ?></h2><br />
                            <p><?php
                            if(isSet($orderData['lesson_type'])) {
                                echo _($orderData['lesson_type']).' '.__('lesson');
                            }
                            ?></p>
                        </div>
                        <?php
                            if(isSet($orderData)) {
                                echo $this->element('Order'.DS.'nav', array('orderData'=>$orderData));
                            }
                        ?>
                    </div>

                    <?php echo $this->fetch('main'); ?>

                </div> <!-- /cont-span6 -->
                <?php
                if(isSet($upcomingAvailableLessons) && isSet($teacherData)) {
                    echo $this->element('Order'.DS.'teacher_and_upcoming', array(   'upcomingAvailableLessons'  => $upcomingAvailableLessons,
                                                                                    'teacherData'               => $teacherData,
                                                                                    'nextOrderStep'             => (isSet($nextOrderStep) && $nextOrderStep)));
                }
                ?>
            </div> <!-- /cont-span12 -->
        </div> <!-- /row -->
    </div> <!-- /container-inner -->
</Section>