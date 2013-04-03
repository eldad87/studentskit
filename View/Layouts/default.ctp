<?php if(isSet($jsSettings)) { echo $this->element('js_settings', array('jsSettings'=>$jsSettings)); } ?>
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
    <?php echo $this->Html->css(array('basic', 'style-responsive', 'bootstrap', 'bootstrap-responsive', 'jquery-ui/ui-lightness/jquery-ui-1.9.1.custom', 'style'));
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
                                        //'jquery-ui-1.9.1.custom.min',
                                        'jquery-ui/jquery-ui-1.9.1.custom',
										'jquery.form',
										'nano',
										'script',
                                        'bootstrap',
										'slimScroll',
                                        /*'jquery.fineuploader-3.0',
                                        'filesystem',
                                        'test-creator',*/
                                        'lang',
                                        /*'jquery.ba-bbq'*/));
        echo $this->fetch('script');

        echo $this->Js->writeBuffer(); // Write cached scripts
    ?>

    <!-- start Mixpanel -->
    <script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f);b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==
            typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,
            e,d])};b.__SV=1.2}})(document,window.mixpanel||[]);
        mixpanel.init("e8e5d63485eefb9b162e9f8121385a51");</script>
    <!-- end Mixpanel -->
</head>

<body data-spy="scroll" data-target=".subnav" data-offset="50">




    <?php
        echo $this->element('topnavigation');
        echo $this->element('header');
        echo $this->element('Home'.DS.'contact_popup');
    ?>

    <!-- Content
   ================================================== -->

        <?php

        $msg = $this->Session->flash('flash', array('element'=>null));
        if($msg) {
            echo '<div class="fullwidth pull-left"><div class="container-inner">';
            echo $this->Layout->flashMessage(__('Info'), $msg, 'alert-info space9');
            echo '</div></div>';
        }

        echo $this->fetch('content');

        echo $this->element('footer');
        ?>
</body>
<?php
$loginRedirect = Router::url(
    array('controller'=>'/', 'action'=>'login', '?'=>array('login_client'=>'facebook'))
);
echo $this->Facebook->init(array('loginCode'=>'
        if(!jsSettings[\'user_id\']) {
            $(\'#login-popup\').modal(\'hide\');
            //location.reload();
            window.location=\''.$loginRedirect.'\';
        }
')); ?>
</html>