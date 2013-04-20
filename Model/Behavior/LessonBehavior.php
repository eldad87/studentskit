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

    public function validateRequestSubjectId(Model $model, $requestSubjectID){
        $requestSubjectID = $requestSubjectID['request_subject_id'];

        //Load the requested subject
        $requestSubjectData = $model->Subject->findBySubjectId($requestSubjectID);
        if(!$requestSubjectData) {
            $model->invalidate('request_subject_id', __('Invalid request subject'));
        }
        $requestSubjectData = $requestSubjectData['Subject'];

        //Validate its a subject request
        if($requestSubjectData['type']!=SUBJECT_TYPE_REQUEST) {
            $model->invalidate('request_subject_id', __('must be a request subject'));
        }

        //Validate the the 2 subjects share the same type live/video
        if(isSet($model->data[$model->alias]['lesson_type']) && !empty($model->data[$model->alias]['lesson_type'])) {
            if($requestSubjectData['lesson_type']!=$model->data[$model->alias]['lesson_type']) {
                if($requestSubjectData['type']==LESSON_TYPE_LIVE) {
                    $model->invalidate('request_subject_id', __('Please chose a LIVE lesson as a suggestion') );
                } else if($requestSubjectData['type']==LESSON_TYPE_VIDEO) {
                    $model->invalidate('request_subject_id', __('Please chose a VIDEO lesson as a suggestion') );
                }
            }
        }

        //Check that the owner of $requestSubjectID is the main student
        if(isSet($model->data[$model->alias]['student_user_id']) && !empty($model->data[$model->alias]['student_user_id'])) {
            if($model->data[$model->alias]['student_user_id']!=$requestSubjectData['user_id']) {
                $model->invalidate('request_subject_id', __('The main student must be the owner of the requested subject'));
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
}