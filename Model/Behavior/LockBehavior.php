<?php
/**
 * This behavior helps to get a lock on DB records
 */

class LockBehavior extends ModelBehavior {


    private $defaultSettings = array(
        'lock_field'        =>'is_locked',
        'lock_ends_field'   =>'lock_ends',
        'locking_time_out'  =>30
    );

    public function setup(Model $model, $config = array()) {
        $this->settings[$model->alias] = array_merge($this->defaultSettings, $config);

        $lockField      = $model->schema( $this->getSetting($model, 'lock_field') );
        $lockEndsField  = $model->schema( $this->getSetting($model, 'lock_ends_field') );
        $lockTimeOut    = $model->schema( $this->getSetting($model, 'locking_time_out') );

        if(empty($lockField) || empty($lockEndsField) || empty($lockTimeOut)) {
            throw new Exception(sprintf(__('Error in settings for Model %s'), $model->alias));
        }
    }



    private function selectForUpdate(Model $model, $id) {
        $query = 'SELECT '.$this->getSetting($model, 'lock_field').', '.$this->getSetting($model, 'lock_ends_field').'
                    FROM '.$model->table.' AS '.$model->alias.'
                    WHERE '.$model->primaryKey.'='.$id.' FOR UPDATE';


        $results = $model->query($query);
        if($results && is_array($results) && isSet($results['0'])) {
            return $results['0'];
        }

        return false;
    }

    public function lock(Model $model, $id, $lockTimeOut=null, $maxLockTime=HOUR) {
        if(is_null($lockTimeOut)) {
            $lockTimeOut = $this->getSetting($model, 'locking_time_out');
        }

        $model->getDataSource()->begin();
        $results = $this->selectForUpdate($model, $id);
        if(!$results) {
            $model->getDataSource()->rollback();
            return false;
        }

        //Check if locked
        if($results[$model->alias][$this->getSetting($model, 'lock_field')]) {
            //Get end locking time
            $endLocking = $results[$model->alias][$this->getSetting($model, 'lock_ends_field')];
            if($model->Behaviors->attached('Time')) {
               //This model is using the time behavior -> Convert datetime to server time before use
               $endLocking = $model->toServerTime($endLocking);
            }

            //Check end locking time
            if($endLocking>=date('Y-m-d H:i:s')) {
                $model->getDataSource()->rollback();
                --$lockTimeOut;
                if($lockTimeOut<=0) {
                    return false;
                }
                sleep(1);
                return $this->lock($model, $id, $lockTimeOut);
            }
        }

        //Update the record as locked and commit
        $endLocking = date('Y-m-d H:i:s', time() + $maxLockTime );
        if($model->Behaviors->attached('Time')) {
            //This model is using the time behavior -> Convert datetime to server time before use
            $endLocking = $model->toClientTime($endLocking);
        }

        $model->create(false);
        $model->id = $id;
        $model->recursive = -1;
        $model->set(array($this->getSetting($model, 'lock_field')=>1, $this->getSetting($model, 'lock_ends_field')=>$endLocking));
        if(!$model->save()) {
            $model->getDataSource()->rollback();
            return false;
        }

        $model->getDataSource()->commit();
        return true;
    }

    public function unlock(Model $model, $id) {
        $model->getDataSource()->begin();
        $results = $this->selectForUpdate($model, $id);
        if(!$results) {
            $model->getDataSource()->rollback();
            return false;
        }

        $model->create(false);
        $model->id = $id;
        $model->set(array($this->getSetting($model, 'lock_field')=>0));
        if(!$model->save()) {
            $model->getDataSource()->rollback();
            return false;
        }

        return $model->getDataSource()->commit();
    }

    public function getUnlockedRecordsFindConditions(Model $model, $conditions) {
        $conditions[] = array(
            'AND'=>array(
            'OR'=>array(
                array($model->alias.'.'.$this->getSetting($model, 'lock_field')=>0),
                array($model->alias.'.'.$this->getSetting($model, 'lock_field')=>1, $model->alias.'.'.$this->getSetting($model, 'lock_ends_field').' <'=>date('Y-m-d H:i:s')),
                $model->alias.'.'.$this->getSetting($model, 'lock_ends_field').' IS NULL'
            ))
        );

        return $conditions;
    }

    private function getSetting(Model $model, $setting) {
        return $this->settings[$model->alias][$setting];
    }
}
?>