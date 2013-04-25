<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sivan
 * Date: 10/9/12
 * Time: 2:54 PM
 * To change this template use File | Settings | File Templates.
 */

class LayoutHelper extends AppHelper {
    public function ratingNew($rating, $allowChange=true, $class='', $id=null) {

        $rating = round($rating);
        $changeableClass = '';
        if($allowChange) {
            $changeableClass = ' dynamic';
        }
        if($id) {
            $id = ' id="'.$id.'"';
        }

        return '<div class="ratingstar '.$class.'"'.$id.'>
                    <div class="star-box'.$changeableClass.($rating>0 ? ' star-active' : null).' first"></div>
                    <div class="star-box'.$changeableClass.($rating>1 ? ' star-active' : null).'"></div>
                    <div class="star-box'.$changeableClass.($rating>2 ? ' star-active' : null).'"></div>
                    <div class="star-box'.$changeableClass.($rating>3 ? ' star-active' : null).'"></div>
                    <div class="star-box'.$changeableClass.($rating>4 ? ' star-active' : null).'"></div>
                </div>';
    }
    public function rating($rating, $fullHTML=true) {
        if(!$fullHTML) {
            return '/img/star.png';
        }
        return '<div class="pull-left star"><img src="/img/star.png" alt="" title=""></div>';
    }

    public function flashMessage($type, $msg, $class=null) {
        return '
        <div class="alert '.$class.' fade in">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>'.$type.'</strong>
            '.$msg.'
        </div>';
    }

    public function priceTag($oneOnOne, $fullGroupStudentPrice=null, $appendClass=null, $currency='$', $format='html') {
        $priceText = $oneOnOne;
        if($fullGroupStudentPrice) {
            $priceText .= '-'.$fullGroupStudentPrice;
        }
        $priceText .= $currency;


        $class = 'price-tag';
        if(!$oneOnOne) {
            $priceText = __('Free');
            $class .= ' price-tag-free';
        }
        if($appendClass) {
            $class .= ' '.$appendClass;
        }

        return '<div class="'.$class.'"><span>'.$priceText.'</span></div>';
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
        $imageSource = str_replace(array('\/','/\\'), array('/', '/'), $imageSource); //Fix image on calendar tooltip

        if(!$imageSource) {
            if(!$width || !$height) {
                return '/img/img-200x210-blank.jpg';
            }
            return '/img/img-'.$width.'x'.$height.'-blank.jpg';
        }
        if(!$width || !$height) {
            return $imageSource;
        }


        $info = pathinfo($imageSource);
        return str_replace($info['basename'], basename($imageSource, '.'.$info['extension']).'_'.$width.'x'.$height.'.'.$info['extension'], $imageSource);
    }

    public function videoPlayer($link, $image=false, $width=430, $height=220) {

        $this->_View->Html->css('http://vjs.zencdn.net/c/video-js.css', null, array('inline' => false));
        $this->_View->Html->script('http://vjs.zencdn.net/c/video.js', array('inline' => false));

        $info = pathinfo($link);

        $mimiType = false;
        switch(strtolower($info['extension'])) {
            case 'mp4':  $mimiType = 'video/mp4'; break;
            case 'ogv':  $mimiType = 'video/ogg'; break;
            case 'webm': $mimiType = 'video/webm'; break;
            case 'flv':  $mimiType = 'video/x-flv'; break;
            case 'mov':  $mimiType = 'video/x-flv'; break;
            default:
                return false;
                break;
        }

        /*$css = null;
        if($image) {
            $image = 'http://universito.com/img/subjects//50df04b3-f51c-42cd-8b7a-3aa4ded5dd25/50df04b3-f51c-42cd-8b7a-3aa4ded5dd25_436x214.jpg';
            $css = '<style type="text/css">
            #video_player{
                background: url("'.$image.'") no-repeat;
            }
            </style>';
        }*/

        return '<video id="video_player" class="video-js vjs-default-skin" controls width="'.$width.'" height="'.$height.'" preload="auto" data-setup="{}">
                                <source type="'.$mimiType.'" src="'.Router::url('/', true).$link.'">
                            </video>';

    }

    public function scriptBlock($script, $options = array()) {
        $options += array('safe' => true, 'inline' => true, 'type'=>'text/javascript');
        if ($options['safe']) {
            $script  = "\n" . '//<![CDATA[' . "\n" . $script . "\n" . '//]]>' . "\n";
        }
        if (!$options['inline'] && empty($options['block'])) {
            $options['block'] = 'script';
        }
        unset($options['inline'], $options['safe']);

        $attributes = $this->_parseAttributes($options, array('block'), ' ');
        $out = sprintf('<script%s>%s</script>', $attributes, $script);

        if (empty($options['block'])) {
            return $out;
        } else {
            $this->_View->append($options['block'], $out);
        }
    }

    public function requireLogin($vars) {
        if(!$this->_View->getVar('user')) {
            $class = 'requireLogin';
            if(isSet($vars['class'])) {
                $class .= ' '.$vars['class'];
            }
            $vars['class'] = $class;
        }
        return $vars;
    }

    public function wishPopupButton($settings=array()) {
        $defaultSettings = array('name'=>__('Lesson Request'), 'class'=>'btns btn-black pull-right text-color index-blackbtn lesson-request-popup');
        $settings = am($defaultSettings, $settings);

        $settings['class'] .= ' lesson-request'; //JS recognize the button using this


        $name = $settings['name'];
        unset($settings['name']);
        return $this->_View->Html->link($name, '#', $this->requireLogin( $settings ));
    }

    public function toolTip($text, $iClass='space3', $aClass=null, $id=null) {
        $attributes = array('data-placement'=>'left', 'rel'=>'#', 'data-title'=>$text);
        if($aClass) {
            $attributes['class'] = $aClass;
            $aClass = ' class="'.$aClass.'"';
        }
        if($id) {
            $attributes['id'] = $id;
            $id = ' id="'.$id.'"';
        }

        return '<a href="#" '.$id.' rel="tooltip" data-placement="left" data-title="'.$text.'" '.$aClass.'><i class="iconSmall-info '.$iClass.'"></i></a>';

    }

    public function buildLessonTooltipHtml($data=array(), $lessonType=null) {
        if(!$lessonType) {
            $lessonType = $data['lesson_type'];
        }
        $fields = array('duration_minutes'=>__('Duration minutes'), '1_on_1_price'=>__('1 on 1 price'));

        if($lessonType==LESSON_TYPE_LIVE) {
            $fields2 = array( 'datetime'=>__('Datetime'), 'is_public'=>__('Is public'), 'max_students'=>__('Max students'), 'num_of_students'=>__('Num of students'), 'full_group_student_price'=>__('Full group price'));
            $fields = am($fields, $fields2);
        }

        if(isSet($data['1_on_1_price']) && $data['1_on_1_price']==0) {
            $data['1_on_1_price'] = __('Free');

            if(isSet($data['max_students']) && $data['max_students']>1) {
                $data['full_group_student_price'] = $data['1_on_1_price'];
            } else {
                unset($data['full_group_student_price']);
            }
        }

        $return = '<div style=\'text-align: left;\'>';
        foreach($fields AS $fieldKey=>$fieldName) {
            if(!isSet($data[$fieldKey]) || (empty($data[$fieldKey]) && $data[$fieldKey]!=0 )) {
                continue;
            }

            $return .= '<p><strong>'.$fieldName.'</strong> '.$data[$fieldKey].'</p>';
        }

        $return .= '</div>';

        return $return;
    }

    public function styleForInput($extra=array()) {
        $style = array( 'label' => array('class'=>'control-label'),
                        'div'=>array('class'=>'control-group'),
                        'between'=>'<div class="control control1">',
                        'after'=>'</div>',
                        'class'=>'x-large2',
                        'error' => array('attributes' => array('class' => 'textalign-left text-error')),
                        'format'=>array('before', 'error', 'label', 'between', 'input', 'after')
        );

        if(isSet($extra['tooltip'])) {
            $style['after'] = $this->toolTip($extra['tooltip'], 'space11').$style['after'];
        }
        if($extra) {
            $style = am($style, $extra);
        }

        return $style;
    }

    public function lessonTypeIcon($lessonType, $class='') {
        $class .= ' lesson-type';

        return $this->_View->Html->image('icons/'.$lessonType.'.png', array('class'=>$class));
    }

    public function getOrganizerUrl($mainArea, $subArea=null) {
        //http://universito.com/Organizer#%23main-area=/Student/lessons&%23sub-area=/Student/lessonsArchive
        ///Organizer#%23main-area=/Message

        $hash = array();
        if($mainArea) {
            $hash[] = '%23main-area='.$mainArea;
        }
        if($subArea) {
            $hash[] = '%23sub-area='.$subArea;
        }
        $hash = implode('&', $hash);

        return array('controller'=>'Organizer', '#'=>$hash);
    }

    public function stringToJSVar($str) {
        return preg_replace("/\r?\n/", "\\n", addslashes($str));
    }


    public function getTurnNotificationsOffUrl($email, $userId, $isTeacher) {
        $data = json_encode(array(
            'email'     =>$email,
            'user_id'   =>$userId
        ));

        App::uses('Security', 'Utility');
        $dataEncoded = Security::rijndael(
            $data,
            Configure::READ('Security.key'),
            'encrypt'
        );

        $isTeacher = isSet($isTeacher) ? $isTeacher : false;

        $disableNotifications = array(
            'controller'=> ($isTeacher ? 'Teacher' : 'Student'),
            'action'    => 'turnNotificationsOff',
            '?'         => array(
                'data'=>base64_encode($dataEncoded)
            )
        );

        return $disableNotifications;
    }

    public function subjectStatistics($subjectData=array(), $lessonData=array()) {
        $statistics = $subjectData + $lessonData;

        $data = array(
            'category_id'               => $statistics['category_id'],
            'lesson_type'               => $statistics['lesson_type'],
            'language'                  => $statistics['language'],
            'name'                      => $statistics['name'],
            'duration_minutes'          => $statistics['duration_minutes'],
            '1_on_1_price'              => $statistics['1_on_1_price'],
            'max_students'              => $statistics['max_students'],
        );

        if(isSet($statistics['subject_id'])) {
            $data['subject_id']     = $statistics['subject_id'];
            $data['teacher_user_id']= $statistics['user_id'];
        } else {
            $data['wish_list_id']   = $statistics['wish_list_id'];
            $data['student_user_id']        = $statistics['student_user_id'];
        }

        if(isSet( $statistics['full_group_student_price'])) {
            $data['full_group_student_price'] = $statistics['full_group_student_price'];
        }
        if(isSet( $statistics['datetime'])) {
            $data['datetime']                   = $statistics['datetime'];
        }
        if(isSet( $statistics['total_lessons'])) {
            //This data is from the subject
            $data['total_lessons']  = $statistics['total_lessons'];
            $data['students_amount']= $statistics['students_amount'];
            $data['raters_amount']  = $statistics['raters_amount'];
            $data['avarage_rating'] = $statistics['avarage_rating'];
            $data['created']        = $statistics['created'];
        }

        //http://magp.ie/2011/11/29/html5-data-attributes-in-html-and-jquery/
        return htmlspecialchars( json_encode($data), ENT_QUOTES );
    }
}