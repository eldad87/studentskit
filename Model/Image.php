<?php
class Image extends AppModel {
	public $name = 'Image';
	public $useTable = 'images';
	public $primaryKey = 'image_id';

    public $actsAs = array(
        'Uploader.Attachment' => array(
                'image'=>array(
                    'uploadDir'	            => 'img/Image/',
                    'appendNameToUploadDir' =>true,
                    'flagColumn'            =>array('dbColumn'=>'image', 'value'=>10), //Flag DB.table.image with value of 10
                    'name'                  =>'formatImageName',
                    'dbColumn'              =>'image_source',
                    'transforms' => array(
                        array('method'=>'resize','width'=> 200,  'height'=>200,  'append'=>'_resize',   'overwrite'=>true, 'dbColumn'=>false, 'aspect'=>true, 'mode'=>Uploader::MODE_HEIGHT, 'setAsTransformationSource'=>true),
                        array('method'=>'crop', 'width' => 60,   'height'=>60,   'append'=>'_60x60',    'overwrite'=>true, 'dbColumn'=>false),
                        array('method'=>'crop', 'width' => 72,   'height'=>72,   'append'=>'_72x72',    'overwrite'=>true, 'dbColumn'=>false),
                        array('method'=>'crop', 'width' => 78,   'height'=>78,   'append'=>'_78x78',    'overwrite'=>true, 'dbColumn'=>false),
                        array('method'=>'crop', 'width' => 149,  'height'=>182,  'append'=>'_149x182',  'overwrite'=>true, 'dbColumn'=>false),
                        array('method'=>'crop', 'width' => 188,  'height'=>197,  'append'=>'_188x197',  'overwrite'=>true, 'dbColumn'=>false),
                    )
                )
            ),

        'Uploader.FileValidation' => array(
            'image' => array(
                'extension'	=> array('gif', 'jpg', 'png', 'jpeg'),
                'filesize'	=> 1048576,
                'minWidth'	=> 200,
                'minHeight'	=> 200,
                'required'	=> false
            )
        )
    );

    function formatImageName($name, $field, $file) {
        return String::uuid();
    }
}
?>