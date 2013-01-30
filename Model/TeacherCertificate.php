<?php
class TeacherCertificate extends AppModel {
	public $name = 'TeacherCertificate';
	public $useTable = 'teacher_certificates';
	public $primaryKey = 'teacher_certificate_id';
    public $actsAs = array(
        'Uploader.Attachment' => array(
            'image_source' => array(
                'finalPath'     => 'img/teachers/certificates/',
                'nameCallback'  => 'formatImageName',
                'overwrite'     => true,
                'transforms'    => array(
                    'image_resize'      => array('method'=>'resize','width'=> 100,  'height'=>100,  'append'=>'_resize',   'overwrite'=>true,  'aspect'=>true, 'mode'=>'height', 'nameCallback'  => 'formatImageName' ),
                    'image_crop_80x80'  => array('method'=>'crop', 'width' => 80,   'height'=>80,   'append'=>'_80x80',    'overwrite'=>true, 'nameCallback'  => 'formatImageName' ),
                ),
                'transport' => array(
                    'class'     => 's3',
                    'accessKey' => 'AKIAIV2BMVHTLRF64V7Q',
                    'secretKey' => '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',
                    'bucket'    => S3_BUCKET,
                    'region'    => 'us-east-1',
                    'folder'    => 'img/teachers/certificates/'
                )
            )
        ),
        'Uploader.FileValidation' => array(
            'image_source' => array(
                'extension'	=> array('gif', 'jpg', 'png', 'jpeg'),
                'filesize'	=> 1048576,
                'minWidth'	=> 100,
                'minHeight'	=> 100,
                'required'	=> true
            )
        )
    );

    public $belongsTo = array('User');

    public $validate = array(
		'teacher_certificate_id' => array(
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

		
		'name'=> array(
			'between' => array(
                'required'  => true,
                'allowEmpty'=> false,
				'rule'    => array('between', 4, 120),
				'message' => 'Between %d to %d characters'
			)
		),
        'description'=> array(
			'between' => array(
                'required'  => true,
                'allowEmpty'=> false,
				'rule'    => array('between', 4, 2500),
				'message' => 'Between %d to %d characters'
			)
		),

		'date' => array(
			'rule'    		=> 'date',
			'message' 		=> 'Enter a valid date.',
			'allowEmpty' 	=> true,
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
}
?>