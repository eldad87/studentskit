<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts.Emails.text
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php echo $content_for_layout;?>


<?php
if(isSet($userId) && isSet($email)) {
    App::uses('Router', 'Routing');
    config('routes');

    $isTeacher = isSet($isTeacher) ? $isTeacher : false;
    ?>
    Don't want to receive these emails anymore? Unsubscribe here:
    <?php
        echo Router::url(
                $this->Layout->getTurnNotificationsOffUrl($email, $userId, $isTeacher),
                true
            );
    ?>
    Manage your email notification preferences:
    <?php
        if($isTeacher) {
            $profileUrl = $this->Layout->getOrganizerUrl('/Teacher/profile');
        } else {
            $profileUrl = $this->Layout->getOrganizerUrl('/Student/profile');
        }

        echo Router::url(
            $profileUrl,
            true
        );
}
?>

