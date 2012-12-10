<script type="text/javascript">

    function enableNextTabAndUpdateCurrentCreationStage(subjectId, newCreationStage) {
        $.ajax({
            url: jQuery.nano('<?php echo Router::url(array('controller'=>'Teacher', 'action'=>'setSubjectCreationStage', '{subject_id}', '{creation_stage}')) ?>',
                                    {subject_id: subjectId, creation_stage: newCreationStage}),
            type: 'get',
            //data: $(this).serialize(),
            dataType: 'json'

        }).done(function ( data ) {
                if(data['response']['title'][0]=='Error') {
                    //Show error
                    showError('#subjectContainer', data['response']['description'][0], '');
                } else {
                    //Just clear errors
                    showError('#subjectContainer');

                    //Enable next tab

                    //Find the next tab
                    var nextTabId;
                    switch(newCreationStage) {
                        case 1: //Subject done, open meeting tab
                            nextTabId = '#meetingTab';
                        break;
                        case 2: //Meeting done, open files
                            nextTabId = '#filesTab';
                        break;
                        case 3: //Files done, open test
                            nextTabId = '#testsTab';
                        break;
                        case 4: //Tests done, open publish
                            nextTabId = '#publishTab';
                        break;
                        case 5: //Publish done, close this tab and show the subject tab
                            $('#publishTab').hide('slow');
                            nextTabId = '#subjectTab';
                        break;

                    }
                    if(nextTabId) {
                        $(nextTabId).removeClass('disable');
                        initTabs();
                        $( nextTabId + ' a').click();
                    }
                }
        });

    }

    //When click on nextButton - execute enableNextTabAndUpdateCurrentCreationStage(data-subject-id, data-creation-stage)
    function initNextButton() {
        $('.nextButton').click(function() {
            enableNextTabAndUpdateCurrentCreationStage($(this).data('subject-id'), $(this).data('creation-stage'));
        });
    }

    //Init tabs
    $(document).ready(function(){
        initTabs();
    });
</script>
<?php
    $classes = array(
        'subject'   => 'active',
        'meeting'   => 'disable',
        'files'     => 'disable',
        'test'      => 'disable',
        'publish'   => 'disable',
    );

    //Set active/Remove classes according to stage
    $classesKeys = array_keys($classes);
    for($i=0; $i<=$creationStage; $i++) {
        //When stage = 0
        if(isSet($classesKeys[$i-1])) {
            $classes[$classesKeys[$i-1]]  = '';
        }

        //When stage = 5;
        if(isSet($classesKeys[$i])) {
            $classes[$classesKeys[$i]]  = 'active';
        }
    }

    //In case the user finished editing, place his view on the first tab
    if($creationStage==CREATION_STAGE_PUBLISH) {
        $classes[$classesKeys[0]] = 'active';
    }

?>
<div class="cont-span15 cbox-space cbox-space">
    <div class="search-all2 sort-mar" id="subjectContainer">
        <div class="black-line-approv"></div>
        <ul class="booking-nav f-pad-norml um-upcoming f-pad-norml1 tab-menu">
            <li class="<?php echo $classes['subject']; ?>" id="subjectTab"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'subject', $subjectId)); ?>"><?php echo __('Subject'); ?></a></li>
            <li class="<?php echo $classes['meeting']; ?>" id="meetingTab"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'subjectMeeting', $subjectId)); ?>"><?php echo __('Meeting'); ?></a></li>
            <li class="<?php echo $classes['files']; ?>" id="filesTab"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'FileSystem','action'=>'fileSystem', 'subject', $subjectId)); ?>"><?php echo __('Files'); ?></a></li>
            <li class="<?php echo $classes['test']; ?>" id="testsTab"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Tests','action'=>'index', $subjectId)); ?>"><?php echo __('Tests'); ?></a></li>

            <?php
                if($creationStage!=CREATION_STAGE_PUBLISH) {
                    echo '<li class="'.$classes['publish'].'" id="publishTab"><a href="#"  class="load3" rel="'.Router::url(array('controller'=>'Teacher','action'=>'subjectPublish', $subjectId)).'">'.__('Publish').'</a></li>';
                }
            ?>
        </ul>
    </div>
    <div class="clear"></div>
    <div class="fullwidth loadpage">

    </div> <!-- /fullwidth -->
</div> <!-- /cont-span6 -->