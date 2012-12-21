<script type="text/javascript">

    $(document).ready(function(){
        $('#localization').lang({
            langDisplay: '#selcountry',
            lang: '#layout',
            addExistsError:{
                title: '<?php echo __('Error'); ?>',
                msg: '<?php echo __('Already exists'); ?>',
                selector: '.head-countrybar .prioritize-message'
            },
            layoutMessageSelector: '.layout-message',

            saveLangUrl: '<?php echo Router::url(array('controller'=>'Accounts', 'action'=>'setLanguage')); ?>',
            savePrioritizeUrl: '<?php echo Router::url(array('controller'=>'Accounts', 'action'=>'setLanguagesOfRecords')); ?>'

        });
    });
</script>
<!-- Topbar
================================================== -->
<Section class="topbar">
    <div class="topbar-inner">
        <div class="top-left" id="localization">
            <div class="label-txt pull-left space1"><?php echo __('Language'); ?></div>
            <div class="pull-left space1 position">
                <div class="select show-tip pointer" id="selcountry"><?php echo __('Default'); ?></div>
                <!-- country box -->
                <div  id="selcountry-tip" class="header-tooltip-box toolbarbox alltip">
                    <div class="header-tooltip"></div>
                    <div class="head-countrybox">
                        <form class="sk-form">
                            <div class="head-countrybar">
                                <div class="layout-message"></div>
                                <div class="error-message"></div>
                                <label class="countrylabel"><?php echo __('Site Layout'); ?> :</label>
                                <?php echo $this->Form->input('layout', array('options' => Configure::read('template_languages'), 'id'=>'layout', 'label'=>false, 'div'=>false, 'default' => Configure::read('Config.language'), 'class'=>'pull-right')); ?>
                            </div>
                            <div class="head-countrybar">
                                <div class="prioritize-message"></div>
                                <p class="head-countrytext pull-left fullwidth"><?php echo __('Prioritize'); ?> : <i class="iconSmall-info"></i></p>

                                <!-- Lang list -->
                                <ul id="prioritize_lang_list">
                                    <li class="space37 fullwidth pull-left space29" data-lang="eng">
                                        <a href="#" class="color-text remove_lang"><i class="iconSmall-red-cross"></i></a> English
                                    </li>
                                    <li class="space37 fullwidth pull-left space29" data-lang="heb">
                                        <a href="#" class="color-text remove_lang"><i class="iconSmall-red-cross"></i></a> Hebrew
                                    </li>
                                </ul>

                                <p class="space37 fullwidth pull-left space29">
                                    <a href="#" class="add"><i class="iconMedium-add-sub pointer"></i></a>
                                    <?php
                                    echo $this->Form->input('languages', array('options' => $languages, 'label'=>false, 'div'=>false, 'class'=>'space38 lang_list'));
                                    ?>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /country box -->
            </div>
            <div class="pull-left head-languagebox">
                <span id="countary2 pointer" class="select2"><?php echo Configure::read('Config.timezone'); ?></span>
                <?php
                echo $this->Form->input('TZ', array('options' => CakeTime::listTimezones(null, null, false), 'label'=>false, 'div'=>false, 'default' => Configure::read('Config.timezone'), 'class'=>'styled2 pointer', 'onChange'=>'changeTime(\'countary2\', this.value)'));
                ?>
            </div>
        </div> <!-- /pull-left -->

        <div class="top-right">
            <div class="top-right">
                <?php
                if(!$user) {
                    echo '<div class="label-txt pull-left space5">
                                <a data-toggle="modal" data-target="#login-popup">
                                Login/Register
                                <i class="iconMedium-sign"></i>
                                </a>
                        </div>';
                }

                echo $this->element('login_register');
                ?>
            </div> <!-- /pull-right -->


            <?php if(isSet($user['user_id'])) { ?>
            <!--world icon and messageicon box -->


		<div class="top-middle">
            <div class="pull-left position request-box pointer">
                <?php
                $notificationsCount = $this->requestAction(array('controller'=>'Notifications', 'action'=>'getUnreadNotificationsCount'));
                if($notificationsCount['unreadCount']) {
                    echo '<div class="requst-number">',$notificationsCount['unreadCount'],'</div>';
                }
                ?>
                <i class="pull-left iconMedium-world space22 icon show-tip" id="world"></i>
                <div class="header-tooltip-box alltip" id="world-tip">
                    <div class="header-tooltip"></div>
                    <ul class="headerdropdown radius3">
                        <li class="visiter-background">
                            <div class="headeruser"><img src="assets/img/users/img-38x38-1.jpg" alt=""></div>
                            <div class="headeruser-text">
                                <p>Hi, How are you.</p>
                            </div>
                        </li>
                        <li>
                            <div class="headeruser"><img src="assets/img/users/img-38x38-1.jpg" alt=""></div>
                            <div class="headeruser-text">
                                <p>Can you invite me in?</p>
                            </div>
                        </li>
                        <li><a href="#">See all</a></li>
                    </ul>
                </div>
            </div>

            <div class="pull-left position pointer">
                <?php
                $messagesCount = $this->requestAction(array('controller'=>'Message', 'action'=>'getUnreadThreadCount'));
                if($messagesCount['unreadCount']) {
                    echo '<div class="requst-number">',$messagesCount['unreadCount'],'</div>';
                }
                ?>
                <i class="pull-left iconMedium-mail space22 icon show-tip" id="massages"></i>
                <div id="massages-tip" class="header-tooltip-box alltip">
                    <div class="header-tooltip"></div>
                    <ul class="headerdropdown radius3">
                        <li class="visiter-background">
                            <div class="headeruser"><img src="assets/img/users/img-38x38-1.jpg" alt=""></div>
                            <div class="headeruser-text">
                                <p>Hi, How are you.</p>
                            </div>
                        </li>
                        <li>
                            <div class="headeruser"><img src="assets/img/users/img-38x38-1.jpg" alt=""></div>
                            <div class="headeruser-text">
                                <p>Can you invite me in?</p>
                            </div>
                        </li>
                        <li><a href="studentkit-message-innerpage action pressed.html">See all</a></li>
                    </ul>
                </div>
            </div>

            <!-- world icon and messageicon box -->
            <?php } ?>


        </div>
</Section>