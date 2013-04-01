
<?php
    $timezoneSetByUser = Configure::read('Config.timezone_set_by_user');
    if(!$timezoneSetByUser) {

        //Auto load timezone
        echo $this->Html->script('jstz');
    }
?>
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

        //On change TZ
        pAPIObj.loadElement('#timezone', 'change', false, 'post');
        pAPIObj.setAppendCallback('#timezone', 'after', function(data) {
            $('#countary2').html(data['response']['timezone']);
            //location.reload(); //Reload page
        });

        var url = '/Notifications/index/{limit}/1/1/1';
        lmObj.loadMoreButton('#notification-load-more', 'click', '#notification ul', url, {}, 'get', 3);
        lmObj.clearBeforeAppend('#notification-load-more', true);
        lmObj.setItemsCountSelector('#notification-load-more', '#notification ul li', function(totalCount, newCount, limit) {
            if(newCount<limit) {
                $('#notification-load-more').remove(); //css('visibility', 'hidden');
            }
            changeNotificationCountValue( -1 * newCount );
        });

        //On the first click, make the first page items as read
        $('#world').click(function(e){
            //Update only once
            if($(this).data('first-click-update')) {
                return false;
            }
            $(this).data('first-click-update', true);

            //Get notifications ids
            var updateNotificationIds = [];
            $('#notification ul li').each(function(index, element){
                updateNotificationIds.push($(element).data('notification-id'));
            });


            changeNotificationCountValue( -1 * updateNotificationIds.length );

            //Update
            var url = '/Notifications/markAsRead';
            $.ajax({
                url: url,
                type: 'post',
                data: {
                    notification_ids: updateNotificationIds
                },
                dataType: 'json'
            })
        });

        function changeNotificationCountValue(plus) {
            var val = $('#unreadNotificationCount').html();

            val = parseInt(val) + plus;

            if(val<=0) {
                $('#world').unbind();
                $('#unreadNotificationCount').remove();
            } else {
                $('#unreadNotificationCount').html( val );
            }

            //
        }

       /* //Messages
        var url = '/Message/getList/{limit}/{page}';
        lmObj.loadMoreButton('#messages-load-more', 'click', '#massages-tip ul', url, {}, 'get', 3);
        lmObj.clearBeforeAppend('#messages-load-more', true)*/

        <?php
            if(!$timezoneSetByUser) {

                //Auto load timezone
                $this->Html->script('jstz', array('inline'=>false));
                ?>

                //Determent user's timezone
                var timezone = jstz.determine();

                if ( typeof (timezone) !== 'undefined') {
                    console.log(timezone.name());
                    $('#timezone').val(timezone.name());
                    $('#timezone').change();
                }
                <?php
            }
        ?>
    });
</script>
<!-- Topbar
================================================== -->
<Section class="topbar">
    <div class="topbar-inner">
        <div class="top-left" id="localization">

            <div class="position">
            <!-- Layout -->
                <div class="label-txt pull-left space1"><?php echo __('Language'); ?></div>
                <div class="pull-left space1 position">
                    <div class="select show-tip pointer" id="selcountry"><?php
                        $templateLanguages = Configure::read('template_languages');
                        $userLang = Configure::read('Config.language');

                        if(isSet($templateLanguages[$userLang])) {
                            echo __($templateLanguages[$userLang]);
                        } else {
                            echo __(current($templateLanguages));
                        }
                        ?>
                    </div>
                </div>
                <!-- /Layout -->

                <!-- Layout/Prioritize -->
                <div id="selcountry-tip" class="header-tooltip-box toolbarbox alltip">
                    <div class="header-tooltip"></div>
                    <div class="head-countrybox">
                        <form class="sk-form">
                            <!-- layout list -->
                            <div class="head-countrybar">
                                <div class="layout-message"></div>
                                <div class="error-message"></div>
                                <label class="countrylabel"><?php echo __('Site Layout'); ?> :</label>
                                <?php echo $this->Form->input('layout', array('options' => $templateLanguages, 'id'=>'layout', 'label'=>false, 'div'=>false, 'default' => $userLang, 'class'=>'pull-right')); ?>
                            </div>
                            <!-- /layout list -->

                            <!-- Prioritize list -->
                            <div class="head-countrybar">
                                <div class="prioritize-message"></div>
                                <p class="head-countrytext pull-left fullwidth"><?php echo __('Prioritize'); ?> : <i class="iconSmall-info"></i></p>

                                <ul id="prioritize_lang_list">
                                    <?php
                                        if(Configure::read('Config.languages_of_records')) {
                                            foreach(Configure::read('Config.languages_of_records') AS $lan=>$language) {
                                                echo '<li class="space37 fullwidth pull-left space29" data-lang="',$lan,'"><a href="#" class="color-text remove_lang"><i class="iconSmall-red-cross"></i></a> ',$language,'</li>';
                                            }
                                        }
                                    ?>
                                </ul>

                                <p class="space37 fullwidth pull-left space29">
                                    <a href="#" class="add"><i class="iconMedium-add-sub pointer"></i></a>
                                    <?php
                                    echo $this->Form->input('languages', array('options' => $languages, 'label'=>false, 'div'=>false, 'class'=>'space38 lang_list'));
                                    ?>
                                </p>
                            </div>
                            <!-- /Prioritize list -->
                        </form>
                    </div>
                </div>
                <!-- /Layout/Prioritize -->
            </div>

        </div>
        <!-- /top-left-->


        <!-- Timezone -->
        <div class="pull-left head-languagebox">
            <span id="countary2" class="select pointer"><?php echo Configure::read('Config.timezone'); ?></span>
            <?php
                echo $this->Form->input('TZ', array('options' => CakeTime::listTimezones(null, null, false),
                                                    'label'=>false, 'div'=>false, 'default' => Configure::read('Config.timezone'), 'class'=>'styled2 pointer',
                                                    'id'=>'timezone', 'data-target'=>Router::url(array('controller'=>'Accounts', 'action'=>'setTimezone', '{value}'))));
            ?>
        </div>
        <!-- /Timezone -->


        <!-- Notifications -->
        <?php if(isSet($user['user_id'])) { ?>

            <!-- world icon and messageicon box -->
            <div class="top-middle">

                <!-- Notifications -->
                <div class="pull-left position request-box pointer" id="notification">
                    <?php
                    $notificationsCount = $this->requestAction(array('controller'=>'Notifications', 'action'=>'getUnreadNotificationsCount'));

                    if($notificationsCount['unreadCount']) {
                        echo '<div class="requst-number" id="unreadNotificationCount">',$notificationsCount['unreadCount'],'</div>';
                    }


                    ?>
                    <i class="pull-left iconMedium-world space22 icon <?php
                        //Only if we have notifications, set a popup class
                        echo ($notificationsCount['unreadCount'] ? 'show-tip' : '');
                        ?>" id="world"></i>
                    <div class="header-tooltip-box alltip" id="world-tip">
                        <div class="header-tooltip"></div>
                        <ul class="headerdropdown radius3">
                            <?php
                            $notifications = $this->requestAction(array('controller'=>'Notifications', 'action'=>'index', 3, 1, 0));
                            if($notifications['notifications']) {
                                echo $this->element('Topnav'.DS.'notifications', array('notifications'=>$notifications['notifications']));
                            }
                            ?>
                        </ul>

                        <div class="headerdropdown"><a href="#" id="notification-load-more" class="loadMore centered"><strong><?php echo __('Next'); ?></strong></a></div>
                    </div>

                </div>
                <!-- /Notifications -->

                <!-- Messages -->
                <div class="pull-left position pointer">
                    <?php
                    $messagesCount = $this->requestAction(array('controller'=>'Message', 'action'=>'getUnreadThreadCount'));
                    if($messagesCount['unreadCount']) {
                        echo '<div class="requst-number">',$messagesCount['unreadCount'],'</div>';
                    }

                    $threads = $this->requestAction(array('controller'=>'Message', 'action'=>'getList'));
                    ?>
                    <i class="pull-left iconMedium-mail space22 icon <?php
                        //Only if we have messages, set a popup class
                        echo ($threads['threads'] ? 'show-tip' : '');
                        ?>" id="massages"></i>

                    <div id="massages-tip" class="header-tooltip-box alltip">
                        <div class="header-tooltip"></div>
                        <ul class="headerdropdown radius3">
                            <?php



                            if($threads['threads']) {
                                echo $this->element('Topnav'.DS.'threads', array('threads'=>$threads['threads']));
                            }
                            ?>
                        </ul>
                        <div class="headerdropdown">
                            <a href="<?php echo Router::url($this->Layout->getOrganizerUrl('/Message')); ?>" class="loadMore centered"><strong><?php echo __('See All'); ?></strong></a>
                        </div>
                    </div>
                </div>
                <!-- /Messages -->

                <!-- CreditPoints -->
                <div class="pull-left position">
                    <a href="<?php
                        $urlArr = $this->Layout->getOrganizerUrl('/Billing');
                        echo Router::url($urlArr);
                    ?>">
                        <div class="requst-number requst-number-green" id="creditPointsCounter"><?php echo $user['credit_points']; ?></div>
                        <i class="pull-left iconMedium-dollar-tool space11 icon show-tip"></i>
                    </a>
                </div>
                <!-- /CreditPoints -->

           </div>
            <!-- world icon and messageicon box -->
        <?php } ?>
        <!-- /Notifications -->



        <div class="top-right">

            <!-- Register / Login-->

            <?php
            echo '<div class="label-txt pull-right space5">';
            if(!$user) {
                echo '
                            <a data-toggle="modal" data-target="#login-popup" class="pointer">',
                            __('Login/Register')
                            ,'<i class="iconMedium-sign space20"></i>
                            </a>';
            } else {
                $username = $user['username'];
                if(mb_strlen($username)>14) {
                    $username = substr($username, 0, 14).'..';
                }
                echo '<span class="space28">',__('Welcome'),', ',$username,'</span>';

                if($loginClient=='facebook' || (isSet($facebookUser) && $facebookUser)) {
                    echo $this->Facebook->logout(array('label'=>__('Logout'), 'redirect' => '/logout'));
                } else {
                    echo $this->Html->link(__('Logout'), '/logout');
                }
            }
            echo '</div>';

            //Show it always, because user can log-off due to timeout
            echo $this->element('login_register');
            ?>

        </div>

        <div class="pull-right hide" id="ajaxInProgress">
            <div class="progress progress-info progress-striped active">
                <div class="bar" style="width: 100%"></div>
            </div>

        </div>
</Section>