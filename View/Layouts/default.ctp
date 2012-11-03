<?php echo $this->element('js_settings', array('jsSettings'=>$jsSettings));  ?>
<?php echo $this->Html->docType('html5'); ?>
<?php echo $this->Facebook->html(); ?>
<head>
    <meta charset="utf-8" />
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $title_for_layout; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <?php echo $this->fetch('meta');?>


    <!-- styles -->
    <?php echo $this->Html->css(array('basic', 'style', 'style-responsive', 'bootstrap', 'bootstrap-responsive'));
    echo $this->fetch('css');?>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- fav and touch icons -->
    <?php
        echo $this->Html->meta('icon');
        echo $this->Html->meta('apple-touch-icon-precomposed', '/ico/apple-touch-icon-144-precomposed.png', array('rel'=>'apple-touch-icon-precomposed', 'size'=>'144x144', 'type'=>null, 'title'=>null))."\n";
        echo $this->Html->meta('apple-touch-icon-precomposed', '/ico/apple-touch-icon-72-precomposed.png', array('rel'=>'apple-touch-icon-precomposed', 'size'=>'72x72', 'type'=>null, 'title'=>null))."\n";
        echo $this->Html->meta('apple-touch-icon-precomposed', '/ico/apple-touch-icon-57-precomposed.png', array('rel'=>'apple-touch-icon-precomposed', 'type'=>null, 'title'=>null))."\n";
    ?>

    <!-- Javascript
================================================== -->
    <?php
        echo $this->Html->script(array( 
										'jquery-1.8.2',
										'custom-form-elements',
										'jquery-ui-1.9.1.custom.min',
										'nano',
										'script',
                                        'bootstrap-transition',
                                        'bootstrap-alert',
                                        'bootstrap-modal',
                                        'bootstrap-dropdown',
                                        'bootstrap-scrollspy',
                                        'bootstrap-tab',
                                        'bootstrap-tooltip',
                                        'bootstrap-popover',
                                        'bootstrap-button',
                                        'bootstrap-collapse',
                                        'bootstrap-carousel',
                                        'bootstrap-typeahead',
                                        //'application',
										'slimScroll'));
        echo $this->fetch('script');
    ?>



    <script type="text/javascript">
        function changeTime(spanId, val) {
            $('#'+spanId).html(val);
        }

        $(document).ready(function(){
            // For Search Selectbox
            $('.dropdown-menu li').click(function(){
                $('.btn-selt-catg').html('<i class="iconSmall-info btn-selt-catg-info"></i>'+$(this).text()+'<span class="caret btn-selt-catg-arw"></span>');
            });

            <!-- For language Selectbox -->
            $('.header-tooltip-box').css('display','none');

            $("#countrySelect").click(function(){
                $("#countryList").slideToggle();
            });
            $("#notificationIcon").click(function(){
                $("#notificationList").slideToggle();
            });
            $("#messageIcon").click(function(){
                $("#messageList").slideToggle();
            });
        });
    </script>
</head>

<body data-spy="scroll" data-target=".subnav" data-offset="50">

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
            <div class="label-txt pull-left space5"><a href="#">Sign In <i class="iconMedium-sign"></i></a></div>
            <div class="label-txt pull-left"><a href="#">Register</a></div>
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

<header>

    <!-- Navbar
   ================================================== -->
    <section class="navbar">
        <div class="navbar-inner">
            <button type="button" class="btn btn-navbar space6" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <h1><?php echo $this->Html->link('Universito', '/', array('title'=>'Home', 'escape'=>false)); ?></h1>
            <div class="nav-collapse">
                <ul class="nav">
                    <li<?php echo $navButtonSelection['home']       ? ' class="active"' : null; ?>><?php echo $this->Html->link('Home', '/', array('title'=>'Home', 'escape'=>false)); ?></li>
                    <li<?php echo $navButtonSelection['board']      ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>Board</span>', array('controller'=>'forum', 'action'=>'/'), array('title'=>'Board', 'escape'=>false)); ?></li>
                    <li<?php echo $navButtonSelection['account']    ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>Account</span>', array('controller'=>'Student', 'action'=>'/'), array('title'=>'Account', 'escape'=>false)); ?></li>
                    <li<?php echo $navButtonSelection['request']    ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>Lesson request</span>', array('controller'=>'Requests', 'action'=>'/'), array('title'=>'Account', 'escape'=>false)); ?></li>
                    <li<?php echo $navButtonSelection['howItWorks'] ? ' class="active"' : null; ?>><a href="#" title="">How it Works</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </section>
</header>

    <!-- Content
   ================================================== -->

        <?php echo $this->Session->flash();
        echo $this->fetch('content'); ?>

    <!-- Footertop
   ================================================== -->
<footer>
    <div class="temphtml" style="display:none;"></div>
    <div class="mysubjectbox-temp" style="display:none;"></div>
    <!-- Footertop
   ================================================== -->
    <Section class="footer-top">
        <div class="footer-top-inner">
            <div class="span">
                <h2>Link Title One</h2>
                <ul class="footer-widget">
                    <li><a href="#" title="">Link 1</a></li>
                    <li><a href="#" title="">Link 2</a></li>
                    <li><a href="#" title="">Link 3</a></li>
                    <li><a href="#" title="">Link 4</a></li>
                    <li><a href="#" title="">Link 5</a></li>
                </ul>
            </div>
            <div class="span">
                <h2>Link Title Two</h2>
                <ul class="footer-widget">
                    <li><a href="#" title="">Link 1</a></li>
                    <li><a href="#" title="">Link 2</a></li>
                    <li><a href="#" title="">Link 3</a></li>
                    <li><a href="#" title="">Link 4</a></li>
                    <li><a href="#" title="">Link 5</a></li>
                </ul>
            </div>
            <div class="span">
                <h2>Link Title Three</h2>
                <ul class="footer-widget">
                    <li><a href="#" title="">Link 1</a></li>
                    <li><a href="#" title="">Link 2</a></li>
                    <li><a href="#" title="">Link 3</a></li>
                    <li><a href="#" title="">Link 4</a></li>
                    <li><a href="#" title="">Link 5</a></li>
                </ul>
            </div>
            <div class="span">
                <h2>Link Title Four</h2>
                <ul class="footer-widget">
                    <li><a href="#" title="">Link 1</a></li>
                    <li><a href="#" title="">Link 2</a></li>
                    <li><a href="#" title="">Link 3</a></li>
                    <li><a href="#" title="">Link 4</a></li>
                    <li><a href="#" title="">Link 5</a></li>
                </ul>
            </div>
        </div>
    </Section>
    <!-- Footerbottom
   ================================================== -->
    <Section class="footer-bottom">
        <div class="footer-bottom-inner">
            <div class="footer-bottom-left">
                <p>Copyright ©2012 StudentKit. All rights reserved.</p>
            </div>
            <div class="footer-bottom-right">
                <p><a href="#" title="">Privacy Policy</a><a href="#" title="">Terms of Use</a></p>
            </div>
        </div>
    </Section>
</footer>
</body>
</html>