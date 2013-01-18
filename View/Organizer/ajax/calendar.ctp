<div class="cont-span15 cbox-space">
    <div class="search-all">
        <h2 class="pull-left space3"><strong><?php echo __('Calendar'); ?></strong></h2>
    </div>
    <div class="box-in-text">
        <p class="text-box"><?php
            echo __('This is a read-only calendar, that helps you to keep track of your lessons.');
            ?></p>
    </div>
<?php
echo $this->element('Order/calendar', array('allLiveLessons'=>$allLiveLessons, 'inline'=>true, 'isTeacher'=>true));
?>
</div>