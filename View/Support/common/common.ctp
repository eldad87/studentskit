<!-- Containeer
================================================== -->
<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">

                <div class="pull-left space6 fullwidth">
                    <!--Menu -->
                    <div class="categorybox radius3">
                        <h3 class="radius1"><strong><?php echo __('Support'); ?></strong></h3>

                        <ul class="categorystripbox">
                            <li class="<?php echo ($this->params['action']=='about'             ? 'activecategory' : null); ?>"><?php echo $this->Html->link('About', array('action'=>'about')) ?></li>
                            <li class="<?php echo ($this->params['action']=='contact'           ? 'activecategory' : null); ?>"><?php echo $this->Html->link('Contact', array('action'=>'contact')) ?></li>
                            <li class="<?php echo ($this->params['action']=='FAQ'               ? 'activecategory' : null); ?>"><?php echo $this->Html->link('FAQ', array('action'=>'FAQ')) ?></li>
                            <li class="<?php echo ($this->params['action']=='termsAndConditions'? 'activecategory' : null); ?>"><?php echo $this->Html->link('Terms & Conditions', array('action'=>'termsAndConditions')) ?></li>
                            <li class="<?php echo ($this->params['action']=='privacyAndPolicy'  ? 'activecategory' : null); ?>"><?php echo $this->Html->link('Privacy & Policy', array('action'=>'privacyAndPolicy')) ?></li>
                            <li class="<?php echo ($this->params['action']=='refundPolicyAQ'    ? 'activecategory' : null); ?>"><?php echo $this->Html->link('Refund Policy', array('action'=>'refundPolicy')) ?></li>
                        </ul>
                    </div>
                    <!--<div class="categorybox radius3 clearleft">
                        <iframe width="290" height="200" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=%D7%90%D7%9E%D7%94+%D7%98%D7%90%D7%95%D7%91%D7%A8+%D7%A4%D7%A8%D7%99%D7%93%D7%9E%D7%9F,+%D7%94%D7%A8%D7%A6%D7%9C%D7%99%D7%94,+%D7%99%D7%A9%D7%A8%D7%90%D7%9C+8%5C11&amp;sll=32.175514,34.850594&amp;hl=en&amp;ie=UTF8&amp;hq=&amp;hnear=Emma+Tauber+Fridman+8,+Herzliya,+Israel&amp;t=m&amp;ll=32.17554,34.850607&amp;spn=0.01453,0.024805&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
                    </div>-->

                    <!-- end of category filter -->
                    <div class="lesson-wrapper">
                            <div class="pull-left space22 fullwidth">
                                <?php echo $this->fetch('main'); ?>
                            </div>
                    </div>
                </div>
            </div> <!-- /cont-span8 -->
        </div> <!-- /row -->
    </div>
</Section>