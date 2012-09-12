<?php


App::import('Model', 'AppModel');
class PendingUserLesson extends AppModel {
	public $name = 'PendingUserLesson';
	public $useTable = 'pending_user_lessons';
	public $primaryKey = 'user_lesson_id';

	public $belongsTo = array(
					'Teacher' => array(
						'className' => 'User',
						'foreignKey'=>'teacher_user_id',
					),
					'Student' => array(
						'className' => 'User',
						'foreignKey'=>'student_user_id',
					),
					'Subject' => array(
						'className' => 'Subject',
						'foreignKey'=>'subject_id',
						'fields'=>array('avarage_rating', 'image')
					),
					'UserLesson' => array(
						'className' => 'UserLesson',
						'foreignKey'=>'user_lesson_id',
					),
                    'TeacherLesson' => array(
                        'className' => 'TeacherLesson',
                        'foreignKey'=>'teacher_lesson_id',
                    )
				);

    public function beforeValidate($options=array()) {
        //Do validation on UserLessonModel
        //Set validation errors back to this model
    }

    public function joinRequest( $teacherLessonId, $studentUserId=null, $teacherUserId=null, $userLessonId=null ) {
        $this->TeacherLesson->recuesive = -2;
        $tlData = $this->TeacherLesson->findByTeacherLessonId($teacherLessonId);
        if(!$tlData) {
            return false;
        }
        $this->create(false);
        //$this->id = $this->getUnusedUserLessonId();
        return $this->save( array('user_lesson_id'=>$this->getUnusedUserLessonId(), 'status'=>'ACTIVE', 'action'=>'join', 'teacher_lesson_id'=>$teacherLessonId, 'student_user_id'=>$studentUserId, 'teacher_user_id'=>$teacherUserId, 'user_lesson_id'=>$userLessonId, '1_on_1_price'=>$tlData['TeacherLesson']['1_on_1_price']) );
    }

    //$userId must be the student - he is the one that need to pay
    public function lessonRequest( $subjectId, $userId, $datetime=null, $reverseStage=false, $extra=array() ) {
        $this->Subject->recursive = -2;
        $sData = $this->Subject->findBySubjectId($subjectId);
        if(!$sData) {
            return false;
        }

        $this->create(false);
        //$this->id = $this->getUnusedUserLessonId();
        $this->set(array('user_lesson_id'=>$this->getUnusedUserLessonId(), 'status'=>'ACTIVE', 'action'=>'order', 'subject_id'=>$subjectId, 'student_user_id'=>$userId, 'datetime'=>$datetime, 'reverse_stage'=>$reverseStage, '1_on_1_price'=>$sData['Subject']['1_on_1_price'], 'extra'=>json_encode($extra)));
        $this->set($extra);
        return $this->save();
    }

    //$userId must be the student - he is the one that need to pay
    public function reProposeRequest($userLessonId, $byUserId, array $data=array()) {
        if(!$this->UserLesson->findByUserLessonIdAndStudentUserId($userLessonId, $byUserId)) {
            return false;
        }

        $this->set(array('user_lesson_id'=>$userLessonId, 'status'=>'ACTIVE', 'action'=>'negotiate', 'student_user_id'=>$byUserId, 'extra'=>json_encode($data)));
        $this->set($data);
        return $this->save();
    }

    public function acceptRequest( $userLessonId, $byUserId ) {
        if(!$this->UserLesson->findByUserLessonIdAndStudentUserId($userLessonId, $byUserId)) {
            return false;
        }
        $this->set(array('user_lesson_id'=>$userLessonId, 'status'=>'ACTIVE', 'action'=>'accept', 'student_user_id'=>$byUserId));
        return $this->save();
    }

    public function convert($userLessonId) {
        $this->create(false);
        $userLessonData = $this->findByUserLessonId($userLessonId);
        if(!$userLessonData || $userLessonData['PendingUserLesson']['status']!='ACTIVE') {
            return false;
        }
        $userLessonData = $userLessonData['PendingUserLesson'];
        $userLessonData = $this->fixNumeric($userLessonData);

        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();

        $res = false;
        switch($userLessonData['action']) {
            case 'join':
                //joinRequest( $teacherLessonId, $studentUserId=null, $teacherUserId=null, $userLessonId=null ) {

                $res = $ulObj->joinRequest( $userLessonData['teacher_lesson_id'], $userLessonData['student_user_id'], $userLessonData['teacher_user_id'], $userLessonId);
                break;

            case 'order':
                $userLessonData['extra'] = json_decode($userLessonData['extra'], true);
                $userLessonData['extra'] = $this->fixNumeric($userLessonData['extra']);
                $userLessonData['extra']['user_lesson_id'] = $userLessonId;

                //lessonRequest( $subjectId, $userId, $datetime=null, $reverseStage=false, $extra=array() ) {
                $res = $ulObj->lessonRequest( $userLessonData['subject_id'], $userLessonData['student_user_id'], $userLessonData['datetime'],
                                            $userLessonData['reverse_stage'],  $userLessonData['extra']);
                break;

            case 'negotiate':
                $userLessonData['extra'] = json_decode($userLessonData['extra'], true);
                $userLessonData['extra'] = $this->fixNumeric($userLessonData['extra']);
                $res = $ulObj->reProposeRequest($userLessonId, $userLessonData['student_user_id'], $userLessonData['extra']);
                break;
            case 'accept':
                $res = $ulObj->acceptRequest($userLessonId, $userLessonData['student_user_id']);
                break;

        }


        if($res) {
            return $this->delete($userLessonId);
        }
        $this->validationErrors = $ulObj->validationErrors;

        return false;
    }

    private function fixNumeric($extra) {
        $intFields = array('subject_id', 'teacher_lesson_id', 'user_lesson_id', 'student_user_id', 'teacher_user_id', 'max_students', 'duration_minutes');
        $floatFields = array('1_on_1_price', 'full_group_total_price');

        foreach($extra AS $key=>$val) {
            if(is_array($val)) {
                $extra[$key] = $this->fixNumeric($val);
            } else if(in_array($key, $intFields)) {
                $extra[$key] = intval($val);
            } else if(in_array($key, $floatFields)) {
                $extra[$key] = floatval($val);
            }
        }

        return $extra;
    }

    public function cancel($userLessonId) {
        $this->id = $userLessonId;
        return $this->save(array('status'=>'CANCELED'));
    }


    public function getUnusedUserLessonId() {
        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();

        $ulObj->create(false);
        $dummyData = array('subject_id'=>0, 'student_user_id'=>0, 'stage'=>0, 'name'=>0, 'description'=>0, 'language'=>0, 'duration_minutes'=>0, '1_on_1_price'=>0);
        if(!$ulObj->save($dummyData, false)) {
            return false;
        }

        $userLessonId = $ulObj->id;
        $ulObj->delete($ulObj->id);
        return $userLessonId;
    }

}
?>