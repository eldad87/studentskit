<?php
/**
 * This behavior check if there is any translation fields in the request
 *  if so, save the translation
 */
App::import('Behavior', 'Translate');
class AutoTranslateBehavior extends TranslateBehavior {

    public function afterSave(Model $model, $created) {
        parent::afterSave($model, $created);

        $translatedFields = array_merge($this->settings[$model->alias], $this->runtime[$model->alias]['fields']);

        $translations = array();

        //Save translation if any

        App::import('I18n', 'Languages');
        $lang = new Languages();
        foreach( $lang->getLanguageList() AS $locale=>$lang) {
            //Go over each translation field, and check if exists in the translation name, I.e "title"->"title_eng"
            foreach($translatedFields AS $field) {
                if(isSet($model->data[$model->alias][$field.'_'.$locale]) && !empty($model->data[$model->alias][$field.'_'.$locale])) {
                    $translations[$locale][$field] = $model->data[$model->alias][$field.'_'.$locale];
                }
            }
        }

        //No translations were found
        if(!$translations) {
            return true;
        }

        $id = $model->id;

        //Save translations
        foreach($translations AS $locale=>$translationData) {
            $model->locale = $locale;
            $model->create();
            $model->id = $id;
            $model->save($translationData);
        }
    }

    public function setLanguage(Model $model, $locale) {
        $model->locale = $locale;
    }
}
