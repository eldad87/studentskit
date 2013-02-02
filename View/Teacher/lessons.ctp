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
            <li class="active"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsUpcoming')); ?>"><?php echo __('Upcoming'); ?></a></li>
            <li><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsArchive')); ?>"><?php echo __('History'); ?></a></li>
            <li><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsBooking')); ?>"><?php echo __('Booking'); ?></a></li>
            <li class="c-mar3"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsInvitations')); ?>"><?php echo __('Invitations'); ?></a></li>
        </ul>
    </div>
    <div class="clear"></div>
</div> <!-- /cont-span6 -->