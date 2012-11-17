<!-- Topbar
================================================== -->
<Section class="topbar">
    <div class="topbar-inner">
        <div class="top-left">
            <div class="label-txt pull-left space1">Language</div>
            <div class="pull-left space1 position">
                <div class="select show-tip" id="selcountry">Country</div>
                <!-- country box -->
                <div  id="selcountry-tip" class="header-tooltip-box toolbarbox alltip">
                    <div class="header-tooltip"></div>
                    <div class="head-countrybox">
                        <form class="sk-form">
                            <div class="head-countrybar">
                                <label class="countrylabel">Layout :</label>
                                <?php
                                echo $this->Form->input('layout', array('options' => Configure::read('template_languages'), 'label'=>false, 'div'=>false, 'default' => Configure::read('Config.language'),  'onChange'=>'changeTime(\'countary1\', this.value)'));
                                ?>
                            </div>
                            <div class="head-countrybar">
                                <p class="head-countrytext pull-left fullwidth">Prioritize: <i class="iconSmall-info"></i></p>
                                <p  class="space37 fullwidth pull-left"><a href="#" class="color-text"><i class="iconSmall-red-cross"></i></a>  English</p>
                                <p  class="space37 fullwidth pull-left"><a href="#" class="color-text"><i class="iconSmall-red-cross"></i></a>  Hebrew</p>
                                <p  class="space37 fullwidth pull-left"><i class="iconMedium-add-sub"></i>
                                    <?php
                                    echo $this->Form->input('languages', array('options' => $languages, 'label'=>false, 'div'=>false, 'class'=>'space38', 'onChange'=>'changeTime(\'countary1\', this.value)'));
                                    ?>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /country box -->
            </div>
            <div class="pull-left head-languagebox">
                <span id="countary2" class="select2"><?php echo Configure::read('Config.timezone'); ?></span>
                <?php
                echo $this->Form->input('TZ', array('options' => CakeTime::listTimezones(null, null, false), 'label'=>false, 'div'=>false, 'default' => Configure::read('Config.timezone'), 'class'=>'styled2', 'onChange'=>'changeTime(\'countary2\', this.value)'));
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
            <div class="pull-left position request-box">
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

            <div class="pull-left position">
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