<?php
$this->Html->script('jquery.fineuploader-3.0', array('inline'=>false));
$this->Html->script('filesystem', array('inline'=>false));
$this->Html->script('test-creator', array('inline'=>false));
$this->Html->script('jquery.ba-bbq', array('inline'=>false));

$this->Html->css(array('fineuploader'), null, array('inline'=>false));
?>
<script type="text/javascript">
    //Init tabs
    $(document).ready(function(){
        initTabs();
    });
</script>
<section class="container">
    <div class="container-inner">
<?php
    //$showAds
    //echo $this->Html->scriptBlock($this->Watchitoo->initJS($meetingSettings['meeting_id'], $meetingSettings), array('inline'=>(isSet($blank)), 'safe'=>false));
?>

        <div class="row">
            <div class="lesson-box pull-left pad8">
                <h3 class="radius1"><!--5:30 - --><strong><?php echo $lessonName; ?></strong></h3>
                <div class="lesson-box-content file-lesson no-padding-and-border">
                    <?php //echo $this->Watchitoo->embedMeetingJS($meetingSettings['meeting_id'], $meetingSettings); ?>
                </div>
            </div>

<?php
    if(!isSet($blank) || !$blank) {
?>

        <div class="search-all2 sort-mar" id="subjectContainer">
            <div class="black-line-approv"></div>
            <ul class="booking-nav f-pad-norml um-upcoming f-pad-norml1 tab-menu">
                <li class="active" id="filesTab"><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'FileSystem','action'=>'fileSystem', $FS['entity_type'], $FS['entity_id'])); ?>"><?php echo __('Files'); ?></a></li>
                <li class="" id="testsTab"><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'Tests','action'=>'index', $subjectId)); ?>"><?php echo __('Tests'); ?></a></li>
                <li class="" id="communityTab"><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'Lessons','action'=>'community', $subjectId)); ?>"><?php echo __('Community'); ?></a></li>
            </ul>
        </div>
        <div class="clear"></div>
        <div id="sub-area"></div>
</div> <!-- /cont-span6 -->
<?php
    }
?>
        </div>
    </div>
</section>