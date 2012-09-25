<?php
/**
 * This behavior check if there client ask to filter results by language, if so - add the filter
 * if there are no results in that language, try other languages (continues)
 */
class LanguageFilterBehavior extends ModelBehavior {
    public $searchType = 'continues';
    private $query = array();
    private $findQueryType = array();

    public function setup(Model $model, $config = array()) {
        $settings = array(
            'language_field'=>'language',
            'search'        =>'continues', //strict
            'languages'     => null,
        );

        $this->settings[$model->alias] = array_merge($settings, $config);

        $associateModel = $this->getAssociateModelByLanguageField($model);
        if(!$associateModel['model']->schema($associateModel['language_field'])) {
            throw new Exception(sprintf(__('Model %s does no have a language field (%s)'), $associateModel['model']->name, $associateModel['language_field']));
        }

    }

    public function setLanguages(Model $model, $languages) {
        if(!$languages) {
            return false;
        }

        if(!is_array($languages)) {
            $languages = array($languages);
        }

        $this->settings[$model->alias]['languages'] = $languages;
    }

    public function beforeFind(Model $model, $query) {
        if(!$languages = $this->getSettings($model, 'languages', false)) {
            //No need to add any language filtering
            return true;
        }


        //['Model']['language'] = array('he','en');
        $associateModel = $this->getAssociateModelByLanguageField($model);
        $query['conditions'][$associateModel['model']->alias.'.'.$associateModel['language_field']] = $languages;
        //$query['conditions'][$this->getSettings($model, 'language_field', false)] = $languages;

        //Save last query
        $this->settings[$model->alias]['query'] = $query;
        unset($this->settings[$model->alias]['query']['offset']);

        $this->settings[$model->alias]['findQueryType'] = $model->findQueryType;



        return $query;
    }

    public function afterFind(Model $model, $results, $primary){
        $query = $this->getSettings($model, 'query', true);
        $findQueryType = $this->getSettings($model, 'findQueryType', true);

        if(!$languages = $this->getSettings($model, 'languages', true)) {
            //No need to add any language filtering
            return true;
        }



        if($this->getSettings($model, 'search', false)!='continues') {
            //No need to add any other languages
            return true;
        }



        $associateModel = $this->getAssociateModelByLanguageField($model);


        //Check if there is any limit.
        if(isSet($query['limit']) && !empty($query['limit'])) {
            //We're on the last page that contains the results, I.e we have 45 results in our DB
            //user asked for page 3 with 10 results per page - page #5 will have 5 results.
            //Now it's time to load additional 5 results with other languages - therefore the limit handling is enough
            if($results) {
                $query['page'] = 1;
                unset($query['offset']);
                $query['limit'] -= count($results);

            } else if(isSet($query['page']) && !empty($query['page'])) {


                //If this is an associate model - count disable association, therefore, lets override it.
                if($associateModel['model']->name!==$model->name) {
                    $model->recursive = 2;
                }

                //Calculate how many pages already been passed
                /**
                 * 45 (original languages) - 25 (other languages)
                 *
                 * Conditions to find other languages:
                 * page 5, limit 10 = offset 0, limit 5
                 * page 6, limit 10 = offset 5, limit 10
                 * page 7, limit 10 = offset 15, limit 10
                 *
                 *
                 */
                $count = $model->find('count', array('conditions'=>$query['conditions']));
                if($count) {
                    //Get pages that the original languages are in
                    $pages = ceil($count/$query['limit']);

                    if($pages>0) {
                        $query['page'] -= $pages;
                        $query['offset'] = $query['page']*$query['limit'];
                    }

                    //Get offset
                    $query['offset'] -= $count%$query['limit'];

                    unset($query['page']);

                }

            }

            //No more records are needed
            if($query['limit']<=0) {
                return $results;
            }

        }

        //Remove the default languages from query
        unset($query['conditions'][$associateModel['model']->alias.'.'.$associateModel['language_field']]);
        //unset($query['conditions'][$this->getSettings($model, 'language_field', false)]);

        //Remove results with the current languages
        $query['conditions'][] = array('NOT'=>array($associateModel['model']->alias.'.'.$associateModel['language_field']=>$languages));

        //$query['conditions'][] = array('NOT'=>array($this->getSettings($model, 'language_field', false)=>$languages));

        //Find other languages - PHP bug - isSet(query['order'][0]) return false even if the key exists
        if(isSet($query['order']) && is_array($query['order']) && array_key_exists(0, $query['order']) && !$query['order'][0]) {
            unset($query['order'][0]);
        }

        $additionalResults = $model->find($findQueryType, $query);

        //Append the additional results
        if($additionalResults) {
            if(!$results) {
                $results = $additionalResults;
            } else {
                $results = am($results, $additionalResults);
            }
        }

        return $results;
    }

    private function getSettings(Model $model, $setting, $remove=true) {
        if(isSet($this->settings[$model->alias][$setting]) && !empty($this->settings[$model->alias][$setting])) {

            $val = $this->settings[$model->alias][$setting];
            if($remove) {
                unset($this->settings[$model->alias][$setting]);
            }
            return $val;
        }

        return false;
    }

    private function getAssociateModelByLanguageField($model) {
        $langField = $this->getSettings($model, 'language_field', false);
        $langFieldArr = explode('.', $langField);
        if(count($langFieldArr)>1) {
            if($langFieldArr[0]!=$model->name) {
                return array('model'=>($model->{$langFieldArr[0]}), 'language_field'=>$langFieldArr[1]);
            }

            $langField = $langFieldArr[1];
        }

        return array('model'=>$model, 'language_field'=>$langField);
    }
}
?>