<?php
App::import('Model', 'AppModel');
abstract class SolrSearch extends AppModel
{
    protected $solrCore;

    abstract protected function beforeSearchBind();

    public function searchSuggestions($query) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($this->solrCore);
        $query['search_fields'] = false;
        $query = $this->_solrDefaultQueryParams($query);

        return $solrObj->suggest( $query, (($query['page']-1)*$query['limit']), $query['limit'] );
    }

    public function search( $query ) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($this->solrCore);

        $query = $this->_solrDefaultQueryParams($query);
        $results = $solrObj->query( $query, array($this->primaryKey), (($query['page']-1)*$query['limit']), $query['limit'] );
        if(!$results || !isSet($results->response->numFound) || !$results->response->numFound) {
            return array();
        }

        $return = array();
        if(!$results->response->docs) {
            return $return;
        }

        //Build conditions
        $conditions = array();

        foreach($results->response->docs AS $doc) {
            if(!isSet($conditions[$this->primaryKey])) {
                $conditions[$this->primaryKey] = array();
            }
            $conditions[$this->primaryKey][] = $doc->{$this->primaryKey};
        }


        $this->beforeSearchBind();
        $return['records'] = $this->find('all', array('conditions'=>$conditions));

        if(isSet($results['facet_counts']['facet_fields'])) {
            $facetName = key($results['facet_counts']['facet_fields']);
            $return['facet']['name'] = $facetName;
            $return['facet']['results'] = (array) $results['facet_counts']['facet_fields'][$facetName];
        }

        //pr($return);
        return $return;
    }

    protected function disable($id) {
        App::import('Vendor', 'Solr');
        $solrObj = new Solr($this->solrCore);
        return $solrObj->removeDocumentById($id);
    }


    private function _solrDefaultQueryParams($query) {
        if(isSet($query['fq']['category_id'])) {
            App::import('Model', 'Category');
            $cObj = new Category();
            $hierarchy = $cObj->getPathHierarchy($query['fq']['category_id'], false);

            $query['facet'] = array('field'=>'categories', 'mincount'=>1);
            if($hierarchy) {
                //$query['fq']['categories'] = $hierarchy; //Remove all subjects that not related to this category
                $query['fq'][] = '{!raw f=categories}'.$hierarchy;


                $hierarchy = explode(',', $hierarchy);
                $hierarchy[0]++;
                $query['facet']['prefix'] = implode(',', $hierarchy);
            } else {
                $query['facet']['prefix'] = '1,';
            }

            unset($query['fq']['category_id']);


            //$query['fq'][] = '{!raw f=categories}1,2';
        }

        if(isSet($query['search']) && !isSet($query['search_fields'])) {
            $query['search_fields'] = array('name'=>5, 'description'=>0.4);
        }

        if(!isSet($query['page'])) {
            $query['page'] = 1;
        }
        if(!isSet($query['limit'])) {
            $query['limit'] = 12;
        }

        return $query;
    }


}