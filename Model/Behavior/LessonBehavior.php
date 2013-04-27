<?php
class LessonBehavior extends ModelBehavior {

    public function isFutureDatetime(Model $model, $datetime) {
        if(isSet($datetime['datetime']) && is_array($datetime)) {
            $datetime = $datetime['datetime'];
        }

        return $model->toServerTime($datetime)>=$model->timeExpression( 'now', false );
    }
    //Make sure date time is 1 hour or more from now
    public function isFuture1HourDatetime(Model $model, $datetime) {
        if(isSet($datetime['datetime']) && is_array($datetime)) {
            $datetime = $datetime['datetime'];
        }

        return $model->toServerTime($datetime)>=$model->timeExpression( 'now +1 hour', false );
    }

    public function validateWishListId(Model $model, $requestWishListID){

        App::import('Model', 'WishList');
        $wishListModel = new WishList();

        //Load the requested subject
        $wishData = $wishListModel->findByWishListId($requestWishListID['wish_list_id']);
        if(!$wishData) {
            $model->invalidate('wish_list_id', __('Invalid wish'));
        }
        $wishData = $wishData['WishList'];


        //Validate the the 2 subjects share the same type live/video
        if(isSet($model->data[$model->alias]['lesson_type']) && !empty($model->data[$model->alias]['lesson_type'])) {
            if($wishData['lesson_type']!=$model->data[$model->alias]['lesson_type']) {
                if($wishData['type']==LESSON_TYPE_LIVE) {
                    $model->invalidate('wish_list_id', __('Please chose a LIVE lesson as a suggestion') );
                } else if($wishData['type']==LESSON_TYPE_VIDEO) {
                    $model->invalidate('wish_list_id', __('Please chose a VIDEO lesson as a suggestion') );
                } else if($wishData['type']==LESSON_TYPE_COURSE) {
                    $model->invalidate('wish_list_id', __('Please chose a COURSE as a suggestion') );
                }
            }
        }

        //Check that the owner of $requestSubjectID is the main student
        if(isSet($model->data[$model->alias]['student_user_id']) && !empty($model->data[$model->alias]['student_user_id'])) {
            if($model->data[$model->alias]['student_user_id']!=$wishData['student_user_id']) {
                $model->invalidate('wish_list_id', __('The main student must be the owner of the wish'));
            }
        }

        return true;
    }

    /* Taken from Subject model - start */
    public function fullGroupStudentPriceCheck( Model $model, $price ) {
        if(!isSet($model->data[$model->alias]['max_students']) || empty($model->data[$model->alias]['max_students'])) {
            $model->invalidate('max_students', __('Please enter a valid max students (1 or more)'));
            //return false;
        } else if(	isSet($model->data[$model->alias]['full_group_student_price'])) {

            if(isSet($model->data[$model->alias]['1_on_1_price']) && $model->data[$model->alias]['1_on_1_price']) {


                $perStudentCommission = Configure::read('per_student_commission');
                if( ($model->data[$model->alias]['full_group_student_price']>$model->data[$model->alias]['1_on_1_price']) || //FGSP is greater then 1on1price
                    ($perStudentCommission>=$model->data[$model->alias]['full_group_student_price'])) { //Check FGSP is greater then commission

                    $model->invalidate('full_group_student_price',
                        sprintf(__('Must be greater then %01.2f, and less or equal to 1 on 1 price (%01.2f)'),
                            $perStudentCommission, $model->data[$model->alias]['1_on_1_price']) );
                }
            } else {
                $model->data[$model->alias]['full_group_student_price'] = null;
            }

        }
        return true;
    }
    /*public function maxStudentsCheck( $maxStudents ) {
        if($maxStudents['max_students']>1 && (!isSet($model->data[$model->alias]['full_group_student_price']) || !$model->data[$model->alias]['full_group_student_price'])) {
            $model->invalidate('full_group_student_price', __('Please enter a valid full group student price or set Max students to 1'));
            //return false;
        }
        return true;
    }*/

    public function priceRangeCheck( Model $model, $price, $checkingFieldName ) {
        if(is_array($price)) {
            $price = $price[$checkingFieldName];
        }

        if($price==0) { //I.e free
            return true;
        }

        $perStudentCommission = Configure::read('per_student_commission');
        if($perStudentCommission>=$price) {
            $model->invalidate($checkingFieldName, sprintf(__('Must be greater than %01.2f, or set 0 for a FREE lesson'), $perStudentCommission));
        }

        return true;
    }


    public static function validateRules( Model $model) {
        $data =& $model->data[$model->alias];

        //Subject only - None public cannot have price/max students
        if( $model instanceof Subject) {
            if(isSet($data['is_public']) &&  $data['is_public']==SUBJECT_IS_PUBLIC_FALSE) {
                $data['1_on_1_price'] = null;
                $data['max_students'] = null;
                $data['full_group_student_price'] = null;

                $model->validator()->remove('1_on_1_price');
                $model->validator()->remove('max_students');
                $model->validator()->remove('full_group_student_price');
            }

            //TL/UL
        } else {

            $exists = $model->exists(!empty($data[$model->primaryKey]) ? $data[$model->primaryKey] : null);

            //Validate that users does not order/schedule TL/UL of private subjects - unless they in a course
            if(!$exists && isSet($data['subject_id'])) {
                //Load subject data
                $model->Subject->recursive = -1;
                $subjectData = $model->Subject->findBySubjectId($data['subject_id']);
                $subjectData = $subjectData['Subject'];

                //Private subject
                if($subjectData['is_public']==SUBJECT_IS_PUBLIC_FALSE &&
                    //Not part of a course
                    (!isSet($data['course_id']) ||
                    !empty($data['course_id']) ||
                    isSet($data['course_schedule_id']) ||
                    empty($data['course_schedule_id']) )) {

                    $model->invalidate('subject_id', ___('Cannot use private subject'));
                }
            }


        }

        //Discount/max students can apply to live lessons only!
        $lessonType = self::getLessonType($model);
        if($lessonType==LESSON_TYPE_VIDEO) {
            $data['duration_minutes'] = null;
            $data['max_students'] = null;
            $data['full_group_student_price'] = null;

            $model->validator()->remove('duration_minutes');
            $model->validator()->remove('max_students');
            $model->validator()->remove('full_group_student_price');
        }

        //1 student
        if(isSet($data['max_students']) && $data['max_students']==1) {
            $data['full_group_student_price'] = null;
            $model->validator()->remove('full_group_student_price');
        }

        //Free lesson
        if(isSet($data['1_on_1_price']) && !$data['1_on_1_price']) {
            $data['full_group_student_price'] = null;
            $model->validator()->remove('full_group_student_price');
        }
    }

    private static function getLessonType( Model $model) {
        $objData =& $model->data[$model->name];

        if(isSet($objData['lesson_type'])) {
            return $objData['lesson_type'];
        }

        //Find object PK
        $objId = false;
        if($model->id) {
            $objId = $model->id;
        } else if(isSet($objData[$model->primaryKey]) && !empty($objData[$model->primaryKey])) {
            $objId = $objData[$model->primaryKey];
        }
        if(!$objId) {
            return true;
        }

        //Load object data
        $foundData = $model->find('first', array('conditions'=>array($model->primaryKey=>$objId)));
        if(!$foundData) {
            return false;
        }

        return  $foundData[$model->name]['lesson_type'];
    }
}