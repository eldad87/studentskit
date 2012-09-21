<?php
/**
 * This behavior
 *  on save: convert user datetime into server datetime
 *  on find: convert server datetime into user datetime
 */

App::uses('CakeTime', 'Utility');
class TimeBehavior extends ModelBehavior {

    public function setup(Model $model, $config = array()) {
        if(!Configure::read('Config.timezone')) {
            Configure::write('Config.timezone', date_default_timezone_get());
        }

        if($config) {
            $this->settings[$model->alias] = $config;
        } else {
            $this->settings[$model->alias] = $this->getAllFieldsOfType($model, 'datetime');
        }
    }

    /**
     *
     * Create a save-able datetime fit to server timezone
     * @param Model $model
     * @param string $timeExpression
     * @param bool $expression
     * @return string
     */
    public function timeExpression(Model $model, $timeExpression='now', $expression=true) {
        //CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now'))

        $datetime = date('Y-m-d H:i:s', is_numeric($timeExpression) ? $timeExpression : strtotime($timeExpression));
        if(!$expression) {
            return $datetime;
        }
        return $model->getDataSource()->expression('\''.$datetime.'\'');
    }
    public function toClientTime(Model $model, $serverDatetime, $format='Y-m-d H:i:s', $tz=null) {
        return date($format, CakeTime::toUnix($serverDatetime, ($tz ? $tz : Configure::read('Config.timezone')) ));
    }
    public function toServerTime(Model $model, $userDatetime) {
        return CakeTime::toServer($userDatetime, Configure::read('Config.timezone'), 'Y-m-d H:i:s');
    }

    public function beforeSave(Model $model) {
        //Change datetime fields into server
        $model->data[$model->alias] =  $this->convert($model, $model->data[$model->alias], 'toServer');
        return true;
    }

    public function afterSave(Model $model, $created) {
        //Change ALL dates into user datetime
        return $this->convert($model, $model->data, 'toClient');
    }

    public function beforeFind(Model $model, $query) {
       //TODO: convert user datetime fields to server time
        return $query;
    }

    public function afterFind(Model $model, $results, $primary){
        return $this->convert($model, $results, 'toClient');
    }

    private function convert(Model $model, $results, $action){
        if(!isSet($this->settings[$model->alias]) || !$this->settings[$model->alias]) {
            return $results;
        }


        if(isSet($results[0])) {
            foreach($results AS $key=>$result) {
                $results[$key] = $this->_convert($model, $results[$key], $action);
            }
        } else {
            $results = $this->_convert($model, $results, $action);
        }


        return $results;
    }
    private function _convert(Model $model, $result, $action) {
        if(!is_array($result)) {
            return $result;
        } else if(isSet($result[$model->alias])) {
            /*
            $model->alias = 'Profile'
            $result = [Profile] => array(
                   'profile_id'=>...
               ),
               ['User'] => array()
            */
            $modelData =& $result[$model->alias];
        } else {
            /*
               $result = array(
                   'profile_id'=>...
                   ['User'] => array()
               )
            */
            $modelData =& $result;
        }

        //Go over each field of this model
        foreach($this->settings[$model->alias] AS $datetimeFiled) {
            //Field does not exists in result
            if(!isSet($modelData[$datetimeFiled]) || empty($modelData[$datetimeFiled]) ||
                (is_object($modelData[$datetimeFiled]) && isset($modelData[$datetimeFiled]->type) )) { //DB expression
                continue;
            }

            //$time = $modelData[$datetimeFiled];
            if($action=='toClient') {
                //Convert server to user datetime
                $modelData[$datetimeFiled] = $this->toClientTime($model, $modelData[$datetimeFiled]);
            } else {
                //Those fields are created by the server and cannot be modified by the user
                if(in_array($datetimeFiled, array('created', 'modified', 'updated'))) {
                    continue;
                }
                //Convert user to server datetime
                $modelData[$datetimeFiled] = $this->toServerTime($model, $modelData[$datetimeFiled]);
            }
        }


        //Go over model associations
        foreach($model->getAssociated() AS $modelName=>$associationType) {
            if(!isSet($result[$modelName])) {
                continue;
            }
            switch($associationType) {
                case 'hasOne':
                case 'belongsTo':
                case 'hasMany':
                    $result[$modelName] = $this->convert($model->{$modelName}, $result[$modelName], $action);
                break;

                default:
                    throw new Exception(sprintf(__('Association %s is not supported yet'), $associationType));
                break;
            }
        }

        return $result;
    }


    /**
     * Get all fields of type $type
     * @param Model $model
     * @param $type
     * @return array
     */
    private function getAllFieldsOfType(Model $model, $type) {
        $selectedColumns = array();

        $columnTypes = $model->getColumnTypes();
        foreach($columnTypes AS $column=>$cType) {
            if($cType!=$type) {
                continue;
            }
            $selectedColumns[] = $column;
        }

        return $selectedColumns;
    }

    private function setDBTZ() {

    }
}
?>