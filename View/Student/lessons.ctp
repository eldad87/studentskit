<script type="text/javascript">
    //Init tabs
    $(document).ready(function(){
        //initTabsOld();
        initTabs();
    });
</script>
<div class="cont-span15 cbox-space cbox-space">
    <div class="search-all2 sort-mar">
        <div class="black-line-approv"></div>
        <ul class="booking-nav f-pad-norml um-upcoming f-pad-norml1 tab-menu">
            <li class="active"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsUpcoming')); ?>"><?php echo __('Upcoming'); ?></a></li>
            <li><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsArchive')); ?>"><?php echo __('History'); ?></a></li>
            <li><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsBooking')); ?>"><?php echo __('Booking'); ?></a></li>
            <li><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsInvitations')); ?>"><?php echo __('Invitations'); ?></a></li>
            <li class="c-mar3"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'subjectRequests')); ?>"><?php echo __('Wish List'); ?></a></li>
        </ul>
    </div>
    <div class="clear"></div>

    <!--    <a class="black-cent-butn2 long-wid2 fontsize1" href="#">Booking</a>
<a class="black-cent-butn2 long-wid2 fontsize1" href="#">Invitations</a>-->
    <div class="clear"></div>
    <!-- /add-sub -->
</div> <!-- /cont-span6 -->