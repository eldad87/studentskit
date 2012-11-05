<?php
if($response['response']['subjects']) {

    foreach($response['response']['subjects'] AS $newSubject) {
        $newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
        echo '<li class="cont-span4 spc space2">';
        echo $this->element('subject_request',  array(  'subjectId' =>$newSubject['Subject']['subject_id'],
                                                        'teacherUserId'         =>$newSubject['Subject']['user_id'],
                                                        'teacherUsername'       =>$newSubject['Student']['username'],
                                                        'name'                  =>$newSubject['Subject']['name'],
                                                        'description'           =>$newSubject['Subject']['description'],
                                                        'avarageRating'         =>$newSubject['Subject']['avarage_rating'],
                                                        'oneOnOnePrice'         =>$newSubject['Subject']['1_on_1_price'],
                                                        'fullGroupStudentPrice' =>$newSubject['Subject']['full_group_student_price'],
                                                        'imageSource'           =>$newSubject['Subject']['image_source'],
        ));
        echo '</li>';
    }
}
?>