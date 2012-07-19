<?php
class Solr {
    private $client;
    //private $core;

    public function Solr( $core ) {

       $this->core = strtolower($core);
       Configure::load('solr');

        //Chose a rand solr server
        $solrServers = Configure::read( 'solr.servers' );
        $solrServer = $solrServers[array_rand($solrServers, 1)];

        $options = array (
            'hostname'	=> $solrServer['hostname'],
            'port'		=> $solrServer['port'],
            'path'		=> $solrServer['path'].'/'.$solrServer['cores'][$this->core],
            'timeout'	=> Configure::read( 'solr.config.timeout' )
        );

        try {
            $this->client = new SolrClient($options);
        } catch (Exception $e) {
            CakeLog::write('ialbums','Caught exception: '.$e->getMessage());
            return false;
        }

        //Ping the server
        $try = 5;
        while(!$this->client->ping() && $try) {
            $try--;
        }

        if(!$try) {
            return false;
        }
    }

    public function ping() {
        try {
            $return = $this->client->ping();
            return $return->success();
        } catch (Exception $e) {
            CakeLog::write('solr', 'options: '.var_export($this->client->getOptions(),true).', Message: '.$e->getMessage());
            return false;
        }
    }


    public function addDocument( $fields ) {
        $doc = new SolrInputDocument();

        foreach($fields AS $field=>$value) {
            if(is_array($value)) {
                foreach($value AS $val) {
                    $doc->addField($field, $val);
                }
            } else {
                $doc->addField($field, $value);
            }
        }

        try {
            $this->client->addDocument($doc);
            $updateResponse = $this->client->commit();
            return $updateResponse->success();
        } catch (Exception $e) {
            CakeLog::write('solr', 'options: '.var_export($this->client->getOptions(),true).', Message: '.$e->getMessage());
            return false;
        }
    }



    public function removeDocumentById( $id ) {
        try {
            $updateResponse =  $this->client->deleteById ( $id );
            $this->client->commit();
            return $updateResponse->success();
        } catch (Exception $e) {
            CakeLog::write('solr', 'options: '.var_export($this->client->getOptions(),true).', Message: '.$e->getMessage());
            return false;
        }
    }

    /**
     * @param $query = array(
     *   'search'=>'what can i say',
     *   'search_fields'=>array('name'=>5, 'description'=>0.4),
     *   'fq'=>array('lang'=>'(EN OR FR'), 'subject_id'=>'(1 OR 4)'),
     *   'facet'=>array('field'=>'categories', 'prefix'=>'4,1,5,10', 'mincount'=>1)
     *   );
     * @param $fields array('name', 'description')
     * @param int $start
     * @param int $rows
     * @return array|bool
     */
    public function query( $query, $fields=array(), $start=0, $rows=50 ) {

        //Build query like q=title:superman^$boost subject:superman^$boost
        $queryString = array();
        if(isSet($query['search_fields'])) {
            foreach ($query['search_fields'] AS $searchField=>$boost) {
                $queryString[$searchField] = $searchField.':'.$query['search'].'^'.$boost;
            }
        }
        $queryString = implode(' ', $queryString);

        $queryObj = new SolrQuery( $queryString );

        //Add filter query fields
        if(isSet($query['fq'])) {
            foreach($query['fq'] AS $f=>$q) {
                //lang:(EN OR FR)
                $queryObj->addFilterQuery( $f.':'.$q );
            }
        }

        //Add facet
        if(isSet($query['facet'])) {
            $queryObj->setFacet ( true );
            if(isSet($query['facet']['field'])) {
                $queryObj->addFacetField( $query['facet']['field'] );
            }
            if(isSet($query['facet']['prefix'])) {
                $queryObj->setFacetPrefix( $query['facet']['prefix'] );
            }
            if(isSet($query['facet']['mincount'])) {
                $queryObj->setFacetMinCount( $query['facet']['mincount'] );
            }
        }

        //Set pagination
        $queryObj->setRows($rows);
        $queryObj->setStart($start); //$query->setParam('start', $start);


        //Add fields to be return
        foreach($fields AS $field) {
            $queryObj->addField($field);
        }


        try {
            $query_response = $this->client->query($queryObj);
            if(!$query_response->success()) {
                return false;
            }
            $response = $query_response->getResponse();
        } catch (Exception $e) {
            CakeLog::write('solr', 'options: '.var_export($this->client->getOptions(),true).', Message: '.$e->getMessage());
            return false;
        }

        if(!isSet($response['response']['numFound']) || !$response['response']['numFound']) {
            //No aliases found
            return array();
        }

        return $response;
    }
}
?>