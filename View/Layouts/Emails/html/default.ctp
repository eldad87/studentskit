<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Universito</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


</head>

<body>
<div style="width:100%; background: #FAFAFA; color:#333;">
    <table cellpadding="0" cellspacing="0" border="0" width="620" align="center" style="font-family:Calibri, Arial, Helvetica, sans-serif; font-size:15px;">
        <tr valign="top">
            <td style="padding-top:20px;">
                <img src="<?php echo Configure::read('public_domain'); ?>/img/logo.png" alt="" />
            </td>
        </tr>
        <tr valign="top">
            <td width="620" style="line-height:11px">
                <img src="<?php echo Configure::read('public_domain'); ?>/img/newletter-tooltip.png"  alt="" />
            </td>
        </tr>

        <tr valign="top">
            <td>
                <table  cellpadding="0" cellspacing="0" border="0"
                        style="background:#eee; border-left:solid 1px #dcdcdc; border-right:solid 1px #dcdcdc; width:620px;">
                    <tr valign="top">
                        <td style="padding:10px;">
                            <?php echo $content_for_layout;?>

                            <div style="margin-left: 5px">
                                <p ><br />Regards,<br />
                                    Universito.com Staff</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="620" style="background:#1d90d5; height:8px; line-height: 8px;">&nbsp;</td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr >
            <td>
                <img src="<?php echo Configure::read('public_domain'); ?>/img/newletter-footer.png" width="620" height="30" alt="" />
            </td>
        </tr>
        <tr >
            <td>
                <p style="font-size: 11px; color: #555;">
                    <?php
                    if(isSet($userId) && isSet($email)) {
                        App::uses('Router', 'Routing');
                        config('routes');


                        $isTeacher = isSet($isTeacher) ? $isTeacher : false;
                        $turnNotificationsOff = $this->Layout->getTurnNotificationsOffUrl($email, $userId, $isTeacher);
                        $turnNotificationsOff['full_base'] = true;

                        if($isTeacher) {
                            $profileUrl = $this->Layout->getOrganizerUrl('/Teacher/profile');
                        } else {
                            $profileUrl = $this->Layout->getOrganizerUrl('/Student/profile');
                        }
                        $profileUrl['full_base'] = true;
                        ?>




                        Don't want to receive these emails anymore? Unsubscribe <?php echo $this->Html->link('here', $turnNotificationsOff);
                        ?>, Manage your email notification preferences <?php echo $this->Html->link('here', $profileUrl);
                    }
                    ?>
                </p>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
