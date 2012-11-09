<?php
class Solr {
    private $client;
    private $options;
    private $core;

    public function Solr( $core ) {

       $this->core = $core;
       Configure::load('solr');

        //Chose a rand solr server
        $solrServers = Configure::read( 'solr.servers' );
        $solrServer = $solrServers[array_rand($solrServers, 1)];

        $this->options = array (
            'hostname'	=> $solrServer['hostname'],
            'port'		=> $solrServer['port'],
            'path'		=> $solrServer['path'].'/'.$solrServer['cores'][$this->core],
            'timeout'	=> Configure::read( 'solr.config.timeout' ),
        );

        try {
            $this->client = new SolrClient($this->options);
        } catch (Exception $e) {
            CakeLog::write('ialbums','Caught exception: '.$e->getMessage());
            return false;
        }

         //pr($this->client->getOptions()); die;

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
                    if($field=='last_modified') {
                        $val = $this->formatToUTC($val);
                    }
                    $doc->addField($field, $val);
                }
            } else {
                if($field=='last_modified') {
                    $value = $this->formatToUTC($value);
                }
                $doc->addField($field, $value);
            }
        }

        try {
            $this->client->addDocument($doc);
            $updateResponse = $this->client->commit(); //https://bugs.php.net/bug.php?id=62332
            return $updateResponse->success();
        } catch (Exception $e) {
            CakeLog::write('solr', 'options: '.var_export($this->client->getOptions(),true).', Message: '.$e->getMessage());
            return false;
        }
    }

    private function formatToUTC($passeddt) {
        // Get the default timezone
        $default_tz = date_default_timezone_get();

        // Set timezone to UTC
        date_default_timezone_set("UTC");

        // convert datetime into UTC
        $utc_format = date("Y-m-d\TG:i:s\Z", strtotime($passeddt));

        // Might not need to set back to the default but did just in case
        date_default_timezone_set($default_tz);

        return $utc_format;
    }



    public function removeDocumentById( $id ) {
        try {
            $updateResponse =  $this->client->deleteById ( $id );
            $this->client->commit(); ///https://bugs.php.net/bug.php?id=62332
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
        $query['search'] = utf8_decode($query['search']); //otherwise we weren't able to search in Hebrew

        //Build query like q=title:superman^$boost subject:superman^$boost
        //Build query like q=name:river naviga miles^5 description:river naviga miles^1
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
                if(is_array($q)) {
                    //lang:(EN OR FR)^5
                    $queryObj->addFilterQuery( $f.':'.$q['value'].'^'.$q['boost'] );
                } else if(is_numeric($f)){
                    $queryObj->addFilterQuery( $q );
                } else {
                    //lang:(EN OR FR)
                    $queryObj->addFilterQuery( $f.':'.$q );
                }
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
            //pr($query_response->getRequestUrl()); die;
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

    public function suggest($query, $start=0, $rows=50) {
        $url = 'http://'.$this->options['hostname'];
        if(isSet($this->options['hostname'])) {
            $url .= ':'.$this->options['port'];
        }
        if(isSet($this->options['path'])) {
            $url .= '/'.$this->options['path'];
        }
        $url .= '/suggest';



        $urlParams = array();


        $queryString = array();
        if(isSet($query['search_fields']) && $query['search_fields']) {
            //Build query like q=name:river naviga miles^5 description:river naviga miles^1
            foreach ($query['search_fields'] AS $searchField=>$boost) {
                $queryString[$searchField] = $searchField.':'.urlencode($query['search']).'^'.$boost;
            }
            $urlParams['q'] = implode(' ', $queryString);
        } else {
            //Build query like q=river naviga miles
            $urlParams['q'] = urlencode($query['search']); //To support Hebrew
        }

        //var_dump($query['fq']); die;
        //Add filter query fields
        if(isSet($query['fq'])) {
            foreach($query['fq'] AS $f=>$q) {
                if(is_array($q)) {
                    //lang:(EN OR FR)^5
                    $urlParams['fq'][] = $f.':'.urlencode($q['value']).'^'.$q['boost'];

                } else if(is_numeric($f)) {
                    //{!raw f=categories}1,2"
                    $urlParams['fq'][] = urlencode($q);
                } else {
                    //lang:(EN OR FR)
                    $urlParams['fq'][] = $f.':'.urlencode($q);
                }
            }

            $urlParams['fq'] = implode('&fq=',$urlParams['fq']);
        }

        //Add facet
        if(isSet($query['facet'])) {
            $urlParams['facet'] = 'on';

            if(isSet($query['facet']['field'])) {
                $urlParams['facet.field'] = $query['facet']['field'];
            }
            if(isSet($query['facet']['prefix'])) {
                $urlParams['facet.prefix'] = $query['facet']['prefix'];
            }
            if(isSet($query['facet']['mincount'])) {
                $urlParams['facet.mincount'] = $query['facet']['mincount'];
            }
        }

        $urlParams['start'] = $start;
        $urlParams['rows'] = $rows;

        $postParams = '';
        foreach($urlParams AS $field=>$value) {
            $postParams .= $field.'='.$value.'&';
        }
        $postParams = substr($postParams, 0, -1); //Remove last &


        $url .= '?'.$postParams;
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        //curl_setopt($ch,CURLOPT_POST, count($postParams)); //To support hebrew
        //curl_setopt($ch,CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $resultsXML = curl_exec($ch);
        curl_close($ch);

        App::import('Utility', 'Xml');
        $results = Xml::toArray(Xml::build($resultsXML));

        if(!isSet($results['response']['lst'][1]['lst']['lst'])) {
            return array();
        }
        $results = $results['response']['lst'][1]['lst']['lst'];



        $return = array('suggestions'=>array(), 'collations'=>array());
        if(isSet($results['@name']) && isSet($results['int']) && isSet($results['arr'])) {
            $results = array($results);
        }

        foreach($results AS $result) {
            if(isSet($result['arr'])) {
                //Suggestion
                $return['suggestions'][] = array(
                    'name'=>$result['@name'],
                    'suggestions'=>(is_array($result['arr']['str']) ? $result['arr']['str'] : array($result['arr']['str']))
                );
            } else if($result['@name']=='collation'){
                //Collection
                //$suggestions = array();
                $name = array();
                if(isSet($result['lst']['str']['@name'])) {
                    $result['lst']['str'] = array($result['lst']['str']);
                }
                foreach($result['lst']['str'] AS $suggestion) {
                    /*$suggestions[] = array(
                        'name'=>$suggestion['@name'],
                        'suggestions'=>array($suggestion['@'])
                    );*/
                    $name[] = $suggestion['@'];
                }

                $return['collations'][]  = implode(' ', $name); //array('name'=>implode(' ', $name), 'suggestions'=>$suggestions);
            }
        }


        //Create a default collection if none.
        if(!$return['collations']) {
            $name = array();
            foreach($return['suggestions'] AS $suggestions) {
                $name[] = $suggestions['suggestions'][0];
            }
            $return['collations'][] = implode(' ', $name);
        }

        return $return;
    }
}
?>