<script type="text/javascript">
    $(document).ready(function(){
        initTabs(false);
    });
</script>
<div class="cont-span6 cbox-space">
    <div class="fullwidth pull-left">
        <h5 class=" pull-left space2"><strong>Tests</strong></h5><div class="clear"></div>

        <ul class="test-list">
            <?php
                foreach($response['response']['results']['tests'] AS $test) {
            ?>
                    <li>
                        <strong><?php echo $test['name']; ?></strong> - <?php echo $test['description']; ?>
                        <div class="pull-right">
                            <?php
                                echo $this->Html->link('<i class="iconSmall-pencil pencilicon actionButton""></i>',
                                                        '#',
                                                        array( 'class'=>'pull-left load3',
                                                            'rel'=>Router::url(
                                                                array('controller'=>'Tests', 'action'=>'manage', $response['response']['results']['subject_id'], $test['test_id'])
                                                            ),
                                                            'escape'=>false
                                                        )
                                );
                            ?>
                            <i class="iconSmall-red-cross redcross actionButton" id="delete_{file_system_id}"></i>
                        </div>
                    </li>
            <?php
                }
            ?>
        </ul>


        <div class="fullwidth pull-left space6">
            <?php
                echo $this->Html->link('ADD', '#', array( 'class'=>'btn-blue extra-pad space11 pull-left load3',
                    'rel'=>Router::url(array('controller'=>'Tests', 'action'=>'manage', $response['response']['results']['subject_id']))));
            ?>

        </div>
    </div> <!-- /fullwidth -->
</div>