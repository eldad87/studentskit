<?php
$config['SignMeUp'] = array(
	'from' => 'StudentsKit.com <donotreplay@studentskit.com>',
	'layout' => 'default',
	'welcome_subject' => __d('SignMeUp','Welcome to StudentsKit.com %username%!'),
	'sendAs' => 'text',
	'activation_template' => 'activate',
	'welcome_template' => 'welcome',
	'password_reset_template' => 'forgotten_password',
	'password_reset_subject' => __d('SignMeUp','Password reset from StudentsKit.com'),
	'new_password_template' => 'new_password',
	'new_password_subject' => __d('SignMeUp','Your new password from StudentsKit.com'),
	'xMailer' => 'StudentsKit.com Email-bot',
);