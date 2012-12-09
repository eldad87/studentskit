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
    <?php echo $this->Html->css(array('basic', 'style-responsive', 'bootstrap', 'bootstrap-responsive', 'jquery-ui/ui-lightness/jquery-ui-1.9.1.custom', 'fineuploader', 'style'));
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
                                        'jquery.fineuploader-3.0',
                                        'filesystem',
                                        'test-creator'));
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



    <?php
        echo $this->element('topnavigation');
        echo $this->element('header');
    ?>

    <!-- Content
   ================================================== -->

        <?php

        echo $this->Session->flash();
        echo $this->fetch('content');

        echo $this->element('footer');
        ?>
</body>
</html>