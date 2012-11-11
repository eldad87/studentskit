<?php
class TeacherCertificate extends AppModel {
	public $name = 'TeacherCertificate';
	public $useTable = 'teacher_certificates';
	public $primaryKey = 'teacher_certificate_id';
    public $actsAs = array(
        'Uploader.Attachment' => array(
            'imageUpload'=>array(
                'uploadDir'	            => 'img/teachers/certificates/',
                'appendNameToUploadDir' => true,
                'flagColumn'            => array('dbColumn'=>'image', 'value'=>1), //Flag DB.table.image with value of IMAGE_SUBJECT_OWNER
                'name'                  => 'formatImageName',
                'dbColumn'              => 'image_source',
                'transforms' => array(
                    array('method'=>'resize','width'=> 100,  'height'=>100,  'append'=>'_resize',   'overwrite'=>true, 'dbColumn'=>'image_resize', 'aspect'=>true, 'mode'=>Uploader::MODE_HEIGHT, 'setAsTransformationSource'=>true),
                    array('method'=>'crop', 'width' => 78,   'height'=>78,   'append'=>'_80x80',    'overwrite'=>true, 'dbColumn'=>'image_crop_80x80'),
                )
            )
        ),

        'Uploader.FileValidation' => array(
            'imageUpload' => array(
                'extension'	=> array('gif', 'jpg', 'png', 'jpeg'),
                'filesize'	=> 1048576,
                'minWidth'	=> 100,
                'minHeight'	=> 100,
                'required'	=> false
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
    public function formatImageName($name, $field, $file) {
        return String::uuid();
    }
}
?>