<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sivan
 * Date: 10/9/12
 * Time: 2:54 PM
 * To change this template use File | Settings | File Templates.
 */

class LayoutHelper extends AppHelper {
    public function rating($rating, $fullHTML=true) {
        if(!$fullHTML) {
            return '/img/star.png';
        }
        return '<div class="pull-left star"><img src="/img/star.png" alt="" title=""></div>';
    }

    public function priceTag($oneOnOne, $fullGroupStudentPrice, $currency='$') {
        $return = $oneOnOne;
        if($fullGroupStudentPrice) {
            $return .= '-'.$fullGroupStudentPrice;
        }

        return $return.$currency;
    }

    /**
     * camelize each word -> Abcd eFg -> Abcd Efg
     * @param $str
     * @return string
     */
    public function formatTitle($str) {
        return Inflector::humanize(strtolower($str));
    }

    /**
     * Get source path, and using naming convention return the right path
     * /img/users/userImage.jpg -> resolution:60x60 -> /img/users/userImage_60x60.jpg
     * @param $imageSource
     * @param null $width
     * @param null $height
     * @return mixed
     */
    public function image($imageSource, $width=null, $height=null) {
        if(!$width || !$height) {
            return $imageSource;
        }
        if(!$imageSource) {
            return false; //TODO: blank image
        }


        $info = pathinfo($imageSource);
        return str_replace($info['basename'], basename($imageSource, '.'.$info['extension']).'_'.$width.'x'.$height.'.'.$info['extension'], $imageSource);
    }

    public function videoPlayer($link) {
        //$teacherData['TeacherAboutVideo'][0]['video_source']
        $info = pathinfo($link);

        $mimiType = false;
        switch(strtolower($info['extension'])) {
            case 'mp4':  $mimiType = 'video/mp4'; break;
            case 'ogv':  $mimiType = 'video/ogg'; break;
            case 'webm': $mimiType = 'video/webm'; break;
            default: return false; break;
        }

        return ' <video id="example_video_1" class="video-js vjs-default-skin" controls width="430" height="220" preload="auto" data-setup="{}">
                                <source type="'.$mimiType.'" src="'.Router::url('/', true).$link.'">
                            </video>';

    }
}