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
                            <h2 class="pull-left"><?php echo $subjectData['name']; ?></h2><br />
                            <p>Live lesson</p>
                        </div>
                        <div class="inner-box-step radius2 border1">
                            <ul class="step-box">
                                <li>
                                    <a href="order-Calender.html" class="active4"><h5>Step 1</h5><p class="fontsize1 centered ">Select Lesson Time</p></a>
                                </li>
                                <li>
                                    <a href="order-ident.html" ><h5>Step 2</h5><p class="fontsize1 centered ">Identification</p></a>
                                </li>
                                <li>
                                    <a href="order-billing.html"><h5>Step 3</h5><p class="fontsize1 centered ">Payment</p></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php echo $this->fetch('main'); ?>

                </div> <!-- /cont-span6 -->
                <?php echo $this->element('Order/teacher_and_upcoming', array('upcomingAvailableLessons'=>$upcomingAvailableLessons, 'teacherData'=>$teacherData)); ?>
            </div> <!-- /cont-span12 -->
        </div> <!-- /row -->
    </div> <!-- /container-inner -->
</Section>