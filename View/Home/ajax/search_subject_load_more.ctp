<?php
if($response['response']['records']) {

    foreach($response['response']['records'] AS $subjectData) {
        $subjectData['Subject']['one_on_one_price'] = $subjectData['Subject']['price'];
        echo '<li class="cont-span4 spc space2">';
        echo $this->element('subject',          array(  'subjectId'             =>$subjectData['Subject']['subject_id'],
                                                        'teacherUserId'         =>$subjectData['Subject']['user_id'],
                                                        'teacherUsername'       =>$subjectData['Teacher']['username'],
                                                        'name'                  =>$subjectData['Subject']['name'],
                                                        'description'           =>$subjectData['Subject']['description'],
                                                        'averageRating'         =>$subjectData['Subject']['average_rating'],
                                                        'price'                 =>$subjectData['Subject']['price'],
                                                        'fullGroupStudentPrice' =>$subjectData['Subject']['full_group_student_price'],
                                                        'imageSource'           =>$subjectData['Subject']['image_source'],
        ));
        echo '</li>';
    }
}
?>