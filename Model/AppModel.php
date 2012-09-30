<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    public $actsAs = array('Time');

    public function resetRelationshipFields() {
        foreach($this->associations() AS $relationName) {
            if(isSet($this->$relationName) && is_array($this->$relationName) && $this->$relationName) {

                //Unbind all models
                //$this->unbindModel( array($relationName=>array(array_keys($this->$relationName))) );


                //remove fields limitation
                $relations = $this->$relationName;
                foreach($relations AS $modelName=>$relationData) {
                    unset($relations[$modelName]['fields']);
                }

                $this->bindModel(array($relationName=>$relations));
            }
        }
    }

    public function unbindAll($exclude=array()) {
        foreach($this->associations() AS $relationName) {
            if(isSet($this->$relationName) && is_array($this->$relationName) && $this->$relationName) {

                //Unbind all models
                //$this->unbindModel( array($relationName=>array(array_keys($this->$relationName))) );


                //remove fields limitation
                foreach($this->$relationName AS $modelName=>$relationData) {
                    if(isSet($exclude[$relationName]) && in_array($modelName,$exclude[$relationName])) {
                        continue;
                    }
                    $this->unbindModel(array($relationName=>array($modelName)));
                }
            }
        }
    }

    /*public function invalidate($field, $value=true) {
        if (!is_array($this->validationErrors)) {
            $this->validationErrors = array();
        }

        $field = explode('.', $field);
        if(count($field)==1) {
            $this->validationErrors[$field[0]][] = $value;
        } else {
            $this->validationErrors[$field[0]][$field[1]][] = $value;
        }
    }*/
}
