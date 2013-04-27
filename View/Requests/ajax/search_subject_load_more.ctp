<?php
if($response['response']['records']) {

    foreach($response['response']['records'] AS $wishData) {
        $wishData['one_on_one_price'] = $wishData['WishList']['price'];
        $wishData['average_rating'] = $wishData['Student']['student_average_rating'];

        echo '<li class="cont-span4 spc space2">';

        echo $this->element('wish', array(
                                                        'wishLisId'             =>$wishData['WishList']['wish_list_id'],
                                                        'name'                  =>$wishData['WishList']['name'],
                                                        'description'           =>$wishData['WishList']['description'],
                                                        'averageRating'         =>$wishData['Student']['student_average_rating'],
                                                        'price'                 =>$wishData['WishList']['price'],
                                                        'imageSource'           =>$wishData['WishList']['image_source'],
                                                        'lessonType'            =>$wishData['WishList']['lesson_type']
        ) );
        echo '</li>';
    }
}
?>