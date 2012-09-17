<?php


App::import('Model', 'AppModel');
class PendingUserLesson extends AppModel {
	public $name = 'PendingUserLesson';
	public $useTable = 'pending_user_lessons';
	public $primaryKey = 'pending_user_lesson_id';

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
        return $this->save( array(/*'user_lesson_id'=>$this->getUnusedUserLessonId(),*/ 'status'=>'ACTIVE', 'action'=>'join',
                                    'teacher_lesson_id'=>$teacherLessonId, 'student_user_id'=>$studentUserId, 'teacher_user_id'=>$teacherUserId, 'user_lesson_id'=>$userLessonId,
                                    '1_on_1_price'=>$tlData['TeacherLesson']['1_on_1_price'], 'subject_id'=>$tlData['TeacherLesson']['subject_id']) );
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
        $this->set(array(/*'user_lesson_id'=>$this->getUnusedUserLessonId(),*/ 'status'=>'ACTIVE', 'action'=>'order',
                            'subject_id'=>$subjectId, 'student_user_id'=>$userId, 'datetime'=>$datetime, 'reverse_stage'=>$reverseStage,
                            '1_on_1_price'=>$sData['Subject']['1_on_1_price'], 'extra'=>json_encode($extra)));
        $this->set($extra);
        return $this->save();
    }

    //$userId must be the student - he is the one that need to pay
    public function reProposeRequest($userLessonId, $byUserId, array $data=array()) {
        if(!$userLessonData = $this->UserLesson->findByUserLessonIdAndStudentUserId($userLessonId, $byUserId)) {
            return false;
        }



        $this->set(array('user_lesson_id'=>$userLessonId, 'status'=>'ACTIVE', 'action'=>'negotiate', 'student_user_id'=>$byUserId,
                            '1_on_1_price'=>$userLessonData['UserLesson']['1_on_1_price'], //'teacher_lesson_id'=>$userLessonData['UserLesson']['teacher_lesson_id'], There is no TeahcerLessonId in re-propose (can't affect join requests)
                            'subject_id'=>$userLessonData['UserLesson']['subject_id'], 'extra'=>json_encode($data)));
        $this->set($data);
        return $this->save();
    }

    public function acceptRequest( $userLessonId, $byUserId ) {
        if(!$userLessonData = $this->UserLesson->findByUserLessonIdAndStudentUserId($userLessonId, $byUserId)) {
            return false;
        }
        $this->set(array('user_lesson_id'=>$userLessonId, 'status'=>'ACTIVE', 'action'=>'accept', 'student_user_id'=>$byUserId,
                            '1_on_1_price'=>$userLessonData['UserLesson']['1_on_1_price'], 'subject_id'=>$userLessonData['UserLesson']['subject_id'],
                            'teacher_lesson_id'=>$userLessonData['UserLesson']['teacher_lesson_id']));
        return $this->save();
    }

    public function execute($pendingUserLessonId) {
        $this->create(false);
        $pendingUserLessonData = $this->findByPendingUserLessonId($pendingUserLessonId);
        if(!$pendingUserLessonData || $pendingUserLessonData['PendingUserLesson']['status']!='ACTIVE') {
            return false;
        }
        $pendingUserLessonData = $pendingUserLessonData['PendingUserLesson'];
        $pendingUserLessonData = $this->fixNumeric($pendingUserLessonData);

        App::import('Model', 'UserLesson');
        $ulObj = new UserLesson();

        $res = false;
        switch($pendingUserLessonData['action']) {
            case 'join':
                //joinRequest( $teacherLessonId, $studentUserId=null, $teacherUserId=null, $userLessonId=null ) {

                $res = $ulObj->joinRequest( $pendingUserLessonData['teacher_lesson_id'], $pendingUserLessonData['student_user_id'], $pendingUserLessonData['teacher_user_id']/*, $userLessonId*/);
                break;

            case 'order':
                $pendingUserLessonData['extra'] = $this->fixNumeric(json_decode($pendingUserLessonData['extra'], true));
                //$pendingUserLessonData['extra'] = $this->fixNumeric($pendingUserLessonData['extra']);
                //$userLessonData['extra']['user_lesson_id'] = $userLessonId;

                //lessonRequest( $subjectId, $userId, $datetime=null, $reverseStage=false, $extra=array() ) {
                $res = $ulObj->lessonRequest( $pendingUserLessonData['subject_id'], $pendingUserLessonData['student_user_id'], $pendingUserLessonData['datetime'],
                                            $pendingUserLessonData['reverse_stage'],  $pendingUserLessonData['extra']);
                break;

            case 'negotiate':
                $pendingUserLessonData['extra'] = json_decode($pendingUserLessonData['extra'], true);
                $pendingUserLessonData['extra'] = $this->fixNumeric($pendingUserLessonData['extra']);
                $res = $ulObj->reProposeRequest($pendingUserLessonData['user_lesson_id'], $pendingUserLessonData['student_user_id'], $pendingUserLessonData['extra']);
                break;
            case 'accept':
                $res = $ulObj->acceptRequest($pendingUserLessonData['user_lesson_id'], $pendingUserLessonData['student_user_id']);
                break;

        }


        if($res) {
            $this->executed($pendingUserLessonId);

            if($pendingUserLessonData['action']=='join' || $pendingUserLessonData['action']=='order') {
                $this->bindUserLessonId($pendingUserLessonId, $ulObj->id);
            }
            return $ulObj->id;
        }
        $this->validationErrors = $ulObj->validationErrors;

        return false;
    }

    /*private function cancelDuplications($pendingUserLessonData, $status) {
        $conditions = array();
        if($pendingUserLessonData['user_lesson_id']) {
            $conditions['user_lesson_id'] = $pendingUserLessonData['user_lesson_id'];
        } else if($pendingUserLessonData['teacher_user_id']) {
            $conditions['teacher_user_id'] = $pendingUserLessonData['teacher_user_id'];
        } else if($pendingUserLessonData['subject_id']) {
            $conditions['subject_id'] = $pendingUserLessonData['subject_id'];
        }

        if($conditions) {
            $conditions['student_user_id'] = $pendingUserLessonData['student_user_id'];
            $conditions['status'] = $status;
            $cancelPending = $this->find('all', array('conditions'=>$conditions, 'fields'=>array('status', $this->primaryKey)));
            if($cancelPending) {
                foreach($cancelPending AS $cp) {
                    $this->cancel($cp[$this->name][$this->primaryKey]);
                }
            }
        }
    }*/

    private function bindUserLessonId($pendingUserLessonId, $userLessonId) {
        $this->create(false);
        $this->id = $pendingUserLessonId;
        return $this->save(array('user_lesson_id'=>$userLessonId));
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

    public function cancel($pendingUserLessonId) {
        $this->id = $pendingUserLessonId;
        return $this->save(array('status'=>'CANCELED'));
    }
    private function executed($pendingUserLessonId) {
        $this->id = $pendingUserLessonId;
        return $this->save(array('status'=>'EXECUTED'));
    }


    /*public function getUnusedUserLessonId() {
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
    }*/
}
?>