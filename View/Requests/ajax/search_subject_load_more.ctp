<?php
if($response['response']['records']) {

    foreach($response['response']['records'] AS $wishData) {
        $wishData['one_on_one_price'] = $wishData['WishList']['1_on_1_price'];
        $wishData['avarage_rating'] = $wishData['Student']['student_avarage_rating'];

        echo '<li class="cont-span4 spc space2">';

        echo $this->element('wish_list', array(
                                                        'wishLisId'             =>$wishData['WishList']['wish_list_id'],
                                                        'name'                  =>$wishData['WishList']['name'],
                                                        'description'           =>$wishData['WishList']['description'],
                                                        'avarageRating'         =>$wishData['Student']['student_avarage_rating'],
                                                        'oneOnOnePrice'         =>$wishData['WishList']['1_on_1_price'],
                                                        'imageSource'           =>$wishData['WishList']['image_source'],
                                                        'lessonType'            =>$wishData['WishList']['lesson_type']
        ) );
        echo '</li>';
    }
}
?>