<?php
    $this->Html->scriptBlock('
    $(document).ready(function() {
        $(\.header-home-button\').click(function() {
            mixpanel.track("Nav. home click");
        });
         $(\.header-forum-button\').click(function() {
            mixpanel.track("Nav. forum click");
        });
         $(\.header-organizer-button\').click(function() {
            mixpanel.track("Nav. organizer click");
        });
         $(\.header-requests-button\').click(function() {
            mixpanel.track("Nav. requests click");
        });
         $(\.header-support-button\').click(function() {
            mixpanel.track("Nav. support click");
        });
    });
    ', array('inline'=>false));


if(!isSet($navButtonSelection)) {
    $navButtonSelection = array(
        'home'=>true,
        'board'=>false,
        'account'=>false,
        'request'=>false,
        'support'=>false,
        //'howItWorks'=>false,
    );
}
?>
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
            <h1><?php echo $this->Html->link('Universito', '/', array('title'=>'Home', 'escape'=>false, 'plugin'=>false)); ?></h1>
            <div class="nav-collapse">
                <ul class="nav">
                    <li<?php echo $navButtonSelection['home']       ? ' class="active"' : null; ?>><?php echo $this->Html->link(__('Home'), '/', array('title'=>'Home', 'escape'=>false, 'plugin'=>false, 'class'=>'header-home-button')); ?></li>
                    <li<?php echo $navButtonSelection['board']      ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>'.__('Community').'</span>', array('controller'=>'forum', 'action'=>'/', 'plugin'=>false), array('title'=>'Board', 'escape'=>false, 'class'=>'header-forum-button')); ?></li>
                    <li<?php echo $navButtonSelection['account']    ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>'.__('Account').'</span>', array('controller'=>'Organizer', 'action'=>'/', 'plugin'=>false), $this->Layout->requireLogin(array('title'=>'Account', 'escape'=>false, 'class'=>'header-organizer-button')) ); ?></li>
                    <li<?php echo $navButtonSelection['request']    ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>'.__('Wish List').'<span>', array('controller'=>'Requests', 'action'=>'/', 'plugin'=>false), array('title'=>'Wish List', 'escape'=>false, 'class'=>'header-requests-button')); ?></li>
                    <li<?php echo $navButtonSelection['support']    ? ' class="active"' : null; ?>><?php echo $this->Html->link('<span>'.__('Support').'<span>', array('controller'=>'Support', 'action'=>'contact', 'plugin'=>false), array('title'=>'Support', 'escape'=>false, 'class'=>'header-support-button')); ?></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </section>
</header>