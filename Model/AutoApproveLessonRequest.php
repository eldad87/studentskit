<?php
class AutoApproveLessonRequest extends AppModel {
	public $name = 'AutoApproveLessonRequest';
	public $useTable = 'auto_approve_lesson_request';
	public $primaryKey = 'teacher_user_id';
	
	public function setSettings( $teacherUserId, $liveAutoEnable, WeekRange $weekRange, $videoAutoEnable ) {

		$this->set(	array(	'teacher_user_id'	=>$teacherUserId,
							'live'				=>$liveAutoEnable,
							'live_range_of_time'=>$weekRange->export(),
							'video'				=>$videoAutoEnable));
		return $this->save();
	}
	
	public function getSettings($teacherUserId) {
        $this->recursive = -1;
		$aalrData = $this->findByTeacherUserId($teacherUserId);
		if(!$aalrData) {
			return array('teacher_user_id'=>$teacherUserId, 'live'=>false, 'live_range_of_time'=>array(), 'video'=>true);//default
		}
		return $aalrData['AutoApproveLessonRequest'];
	}
	
	public function isAutoApprove($teacherUserId, $lessonType, $date=null) {
		$aalrData = $this->getSettings($teacherUserId);
		if($lessonType==LESSON_TYPE_VIDEO) {
			return ($aalrData['video'] ? true : false);
		} else if($lessonType==LESSON_TYPE_LIVE) {
			//TODO: check if there is a lesson during that time. also note for userLesson for subject type request
			
			$weekRange = new WeekRange(json_decode($aalrData['live_range_of_time'], true));
			return $weekRange->isInRange(date('N', $date), date('G', $date));
		}
	}
}


class WeekRange {
	private $settings = array();
	
	const SUNDAY 	= 1;
	const MONDAY 	= 2;
	const TUESDAY 	= 3;
	const WEDNESDAY = 4;
	const THURSDAY 	= 5;
	const FRIDAY 	= 6;
	const SATURDAY 	= 7;

	public function WeekRange($settings=array()) {
		if($settings) {
			foreach($settings AS $day=>$rangeHour) {
				if(isSet($rangeHour['start']) && isSet($rangeHour['end'])) {
					$this->setDay($day, new HourRange($rangeHour['start'], $rangeHour['end']));
				} else {
					$this->setDay($day, new HourRange());
				}
				
			}
		} else {
			$this->setDay(WeekRange::SUNDAY, 	new HourRange());
			$this->setDay(WeekRange::MONDAY, 	new HourRange());
			$this->setDay(WeekRange::TUESDAY, 	new HourRange());
			$this->setDay(WeekRange::WEDNESDAY, new HourRange());
			$this->setDay(WeekRange::THURSDAY, 	new HourRange());
			$this->setDay(WeekRange::FRIDAY, 	new HourRange());
			$this->setDay(WeekRange::SATURDAY, 	new HourRange());
		}
	}
	
	public function setDay($day, HourRange $range) {
		$this->settings[$day] = $range;
	}
	
	public function isInRange( $day, $hour ) {
		if(!isSet($this->settings[$day])) {
			return false;
		}
		return $this->settings[$day]->isInrange($hour);
	}
	
	public function export() {
		$return = array();
		foreach($this->settings AS $day=> $hourRange) {
			$return[$day] = $hourRange->export();
		}
		return $return;
	}
	
	
}

class HourRange {
	private $range = array();
	
	public function HourRange($start=null, $end=null) {
		if(!is_null($start) && !is_null($end) && //Start and End
			$end>$start && $start>=1 && 24>=$end) { //In range
				
			$this->range = array('start'=>$start, 'end'=>$end);
		}
	}
	
	public function isInRange($hour) {
		return ($hour>=$this->range['start'] && $this->range['end']>=$hour);
	}
	
	public function export() {
		return $this->range;
	}
}
?>