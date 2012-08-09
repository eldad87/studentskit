<?php
/**
 * This behavior check if there is any translation fields in the request
 *  if so, save the translation
 */
App::import('Behavior', 'Translate');
class AutoTranslateBehavior extends TranslateBehavior {
    private $userLang = DEFAULT_LANGUAGE;

    public function beforeSave(Model $model, $options = array()) {
        /*//Set lang to default -
        if(Configure::read('Config.language')) {
            $this->userLang = Configure::read('Config.language');
        }
        Configure::write('Config.language', DEFAULT_LANGUAGE);*/

        parent::beforeSave($model, $options);
    }
    public function afterSave(Model $model, $created) {
        parent::afterSave($model, $created);

        $translatedFields = array_merge($this->settings[$model->alias], $this->runtime[$model->alias]['fields']);

        $translations = array();

        //Save translation if any
        foreach(Configure::read('Config.languages') AS $locale=>$lang) {
            $realLocale = str_replace('_', '-', $locale); //Convert en_us -> en-us
            //Go over each translation field, and check if exists in the translation name, I.e "title"->"title_en_us"
            foreach($translatedFields AS $field) {
                if(isSet($model->data[$model->alias][$field.'_'.$locale]) && !empty($model->data[$model->alias][$field.'_'.$locale])) {
                    $translations[$realLocale][$field] = $model->data[$model->alias][$field.'_'.$locale];
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
