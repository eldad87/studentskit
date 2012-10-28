<?php
class TeacherAboutVideo extends AppModel {
	public $name = 'TeacherAboutVideo';
	public $useTable = 'teacher_about_videos';
	public $primaryKey = 'teacher_about_video_id';
    public $actsAs = array(
        'LanguageFilter',

        'Uploader.Attachment' => array(
            'videoUpload'=>array(
                'uploadDir'	            => 'vid/teachers/about_videos/',
                'appendNameToUploadDir' => true,
                //'flagColumn'            => array('dbColumn'=>'image', 'value'=>1), //Flag DB.table.image with value of IMAGE_SUBJECT_OWNER
                'name'                  => 'formatImageName',
                'dbColumn'              => 'video_source'
            )
        ),

        'Uploader.FileValidation' => array(
            'videoUpload' => array(
                'extension'	=> array('webm', 'ogv', 'mp4'),
                'filesize'	=> 104857600, //100MB
                /*'minWidth'	=> 100,
                'minHeight'	=> 100,*/
                'required'	=> true
            )
        )
    );

    public $belongsTo = array('User');

    public $validate = array(
		'teacher_about_video_id' => array(
			'blank' => array(
				'rule'	=> 'blank',
				'on'	=> 'create',
				'message' => 'This field must be left blank'
			),
			'numeric' => array(
				'rule'	=> 'numeric',
				'message' => 'This field must be a numeric value'
			)
		),

	);

    public function beforeValidate($options=array()) {
        parent::beforeValidate($options);

        App::uses('Languages', 'Utils.Lib');
        $lang = new Languages();
        $this->validator()->add('language', 'inList', array(
            'required'	=> 'create',
            'allowEmpty'=> false,
            'rule'    	=> array('inList', array_flip($lang->lists('locale'))),
            'message' 	=> __('Please select a language'),
            'last'      => false
        ));
    }

    public function formatImageName($name, $field, $file) {
        return String::uuid();
    }
}
?>