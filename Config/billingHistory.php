<?php

/* Student action*/

$config['billingHistory.student.add']      = __('{creditPoints} been added to your account');

//Order/Accept/Negotiate funds add
$config['billingHistory.student.lesson.add']      = __('{creditPoints} credit points has been reallocated from your account to "{lessonName}"');
//Accept/Negotiatio funds reduce/Invalid request
$config['billingHistory.student.lesson.reduce']   = __('{creditPoints} credit points has been reallocated from "{lessonName}" to your account');

//Transfer funds to teacher
$config['billingHistory.student.payment']   = __('{creditPoints} credit points has been reallocated from "{lessonName}" to the teacher\'s account');


/* Teacher action*/
//Transfer funds to teacher
$config['billingHistory.teacher.payment']   = __('{creditPoints} credit points has been received as payment for "{lessonName}". Total commission {commission} + gateway fee {gatewayFee}');