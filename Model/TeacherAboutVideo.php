<?php
class TeacherAboutVideo extends AppModel {
	public $name = 'TeacherAboutVideo';
	public $useTable = 'teacher_about_videos';
	public $primaryKey = 'teacher_about_video_id';
    public $actsAs = array(
        'LanguageFilter',

        'Uploader.Attachment' => array(
            'video_source' => array(
                'finalPath'     => 'vid/teachers/about_videos/',
                'nameCallback'  => 'formatImageName',
                'overwrite'     => true,
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'vid/teachers/about_videos/'
                )
            )
        ),
        'Uploader.FileValidation' => array(
            'image_source' => array(
                'extension'	=> array('webm', 'ogv', 'mp4', 'flv', 'mov'),
                'filesize'	=> 104857600, //100MB
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

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['id'] = sprintf('%s.%s', $this->alias, $this->primaryKey); //Uploader
    }

    //Change upload folder
    public function beforeTransport($options) {
        $options['folder'] .= String::uuid() . '/';
        return $options;
    }

    //Remove the "resize-100x100" from transformations file
    public function formatImageName($name, $file) {
        return $this->getUploadedFile()->name();
    }

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

}
?>