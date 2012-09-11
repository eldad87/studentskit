<?php
/**
 * This behavior check if there is any translation fields in the request
 *  if so, save the translation
 */
App::import('Behavior', 'Translate');
class AutoTranslateBehavior extends TranslateBehavior {
    private $findWithTranslations = array();

    public function setFindWithAllTranslations(Model $model) {
        $this->findWithTranslations[$model->alias] = true;
    }

    public function afterFind( Model $model, $results, $primary) {
        $results =  parent::afterFind($model, $results, $primary);
        $locale = $this->_getLocale($model);
        if(!$results || !$locale || !isSet($this->findWithTranslations[$model->alias]) || !$this->findWithTranslations[$model->alias]) {
            return $results;
        }
        unset($this->findWithTranslations[$model->alias]);



        //Append translations into results
        foreach($results AS &$result) {
            //Find translations
            $RuntimeModel = $this->translateModel($model);
            $translations = $RuntimeModel->find('all', array('conditions'=>array('model'=>$model->name, 'foreign_key'=>$result[$model->alias][$model->primaryKey], 'NOT'=>array('locale'=>$locale)),
                                            'fields'=>array('locale', 'field', 'content') ));

            foreach($translations AS $translation) {
                $translation = $translation[$RuntimeModel->alias];

                $result[$model->alias][$translation['field'].'_'.$translation['locale']] = $translation['content'];
            }
        }

        return $results;
    }

    public function afterSave(Model $model, $created) {
        parent::afterSave($model, $created);

        $translatedFields = array_merge($this->settings[$model->alias], $this->runtime[$model->alias]['fields']);

        $translations = array();

        //Save translation if any

        App::uses('Languages', 'Utils.Lib');
        $lang = new Languages();
        foreach( $lang->lists('locale') AS $localeISO6393=>$langName) {
            //Go over each translation field, and check if exists in the translation name, I.e "title"->"title_eng"
            foreach($translatedFields AS $field) {
                if(isSet($model->data[$model->alias][$field.'_'.$localeISO6393]) && !empty($model->data[$model->alias][$field.'_'.$localeISO6393])) {
                    $translations[$localeISO6393][$field] = $model->data[$model->alias][$field.'_'.$localeISO6393];
                }
            }
        }

        //No translations were found
        if(!$translations) {
            return true;
        }

        $id = $model->id;

        //Save translations
        foreach($translations AS $localeISO6393=>$translationData) {
            $model->locale = $localeISO6393;
            $model->create(false);
            $model->id = $id;
            $model->save($translationData);
        }
    }

    public function setLanguage(Model $model, $locale) {
        $model->locale = $locale;
    }
}
