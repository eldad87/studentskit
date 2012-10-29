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
                            <p>Live lesson</p>
                        </div>
                        <?php echo $this->element('Order/nav', array('orderData'=>$orderData)); ?>
                    </div>

                    <?php echo $this->fetch('main'); ?>

                </div> <!-- /cont-span6 -->
                <?php echo $this->element('Order/teacher_and_upcoming', array('upcomingAvailableLessons'=>$upcomingAvailableLessons, 'teacherData'=>$teacherData)); ?>
            </div> <!-- /cont-span12 -->
        </div> <!-- /row -->
    </div> <!-- /container-inner -->
</Section>