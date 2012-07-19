<?php
/**
 *@property Solr $Solr
 */
class SolrController extends AppController {
    public $name = 'Solr';
    public $uses = array('SubjectCategory');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function testCategories() {
        $this->SubjectCategory->create();
        $this->SubjectCategory->set(array('name'=>'main'));
        $this->SubjectCategory->save();

        $this->SubjectCategory->create();
        $this->SubjectCategory->set(array('name'=>'main 2'));
        $this->SubjectCategory->save();

        $id = $this->SubjectCategory->id;
        $this->SubjectCategory->create();
        $this->SubjectCategory->set(array('name'=>'sub main 2', 'parent_subject_category_id'=>$id));
        $this->SubjectCategory->save();

        $id = $this->SubjectCategory->id;
        $this->SubjectCategory->create();
        $this->SubjectCategory->set(array('name'=>'sub main 2 sub', 'parent_subject_category_id'=>$id));
        $this->SubjectCategory->save();

        $this->SubjectCategory->create();
        $this->SubjectCategory->set(array('name'=>'sub main 3 sub', 'parent_subject_category_id'=>$id));
        $this->SubjectCategory->save();
    }

    private function initSolr($core) {
        App::import('Vendor', 'Solr');
        $this->Solr = new Solr($core);
    }

    public function index() {

        $query = array(
            'search'=>'what can i say',
            'search_fields'=>array('name'=>5, 'description'=>0.4),
            'fq'=>array('lang'=>'(EN OR FR)'/*, 'subject_id'=>'(1 OR 4)'*/, 'is_public'=>true),
            'facet'=>array('field'=>'categories', 'prefix'=>'4,1,5,10', 'mincount'=>1)
        );
        $this->initSolr('subject_offer');
        $results = $this->Solr->query( $query );
        pr($results);
        die;
    }




    public function addSubject() {
        App::Import('Model', 'Subject');

        $this->initSolr('subject_offer');

        $document = array(
            'subject_id'		=>1,
            /*'name'				=>'say what',
            'heb_t'				=>'say what',
            'keywords'			=>array('key', 'value', 'hi'),*/
            'categories'        =>array('1,1', '2,1,5', '3,1,5,10', '4,1,5,10,15'),
            'price'			    =>'10,USD',
            'lang'              =>array('EN', 'HE'),
            'lesson_type'       =>LESSON_TYPE_VIDEO,
            'rate'			    =>5,
            'is_public'			=>true,
        );
        $this->Solr->addDocument($document);

        $document = array(
            'subject_id'		=>2,
            'name'				=>'say what say what',
            'heb_t'				=>'say what say what',
            'keywords'			=>array('key', 'now', 'later'),
            'categories'        =>array('1,1', '2,1,5', '3,1,5,10', '4,1,5,10,15'),
            'lang'              =>array('EN', 'FR', 'PL'),
            'lesson_type'       =>LESSON_TYPE_VIDEO,
            'price'			    =>'7,USD',
            'rate'			    =>4.5,
            'is_public'			=>true,
        );
        $this->Solr->addDocument($document);


        $document = array(
            'subject_id'		=>3,
            'name'				=>'wtf how are you',
            'heb_t'				=>'cant be',
            'keywords'			=>array('key', 'now', 'later'),
            'categories'        =>array('1,1', '2,1,5', '3,1,5,10', '4,1,5,10,16'),
            'lang'              =>array('HE'),
            'lesson_type'       =>LESSON_TYPE_LIVE,
            'price'			    =>'4,USD',
            'rate'			    =>3.5,
            'is_public'			=>true,
        );

        $this->Solr->addDocument($document);

        $document = array(
            'subject_id'		=>4,
            'name'				=>'what should i do',
            'heb_t'				=>'no way',
            'keywords'			=>array('key', 'now', 'hi'),
            'categories'        =>array('1,1', '2,1,5', '3,1,5,10', '4,1,5,10,15', '5,1,5,10,15,20'),
            'lang'              =>array('EN', 'HE', 'FR'),
            'lesson_type'       =>LESSON_TYPE_LIVE,
            'price'			    =>'1,USD',
            'rate'			    =>1,
            'is_public'			=>true,
        );
        $this->Solr->addDocument($document);
    }


    /*public function faced() {
        //http://localhost:8080/solr/select/?
        //q=name:say%20what^5%20description:what&
        //fq=lang:HE&fq=subject_id:1&
        //facet=on&facet.field=categories&facet.prefix=4,1,5,10&facet.mincount=1



        $query = new SolrQuery('name:what should i say^5 OR description:what should i say^0.4');
        $query->addFilterQuery( 'lang:(EN OR FR)' );
        $query->addFilterQuery( 'subject_id:(1 OR 4)' );

        $query->setFacet ( true );
        $query->addFacetField( 'categories' );
        $query->setFacetPrefix( '4,1,5,10' );
        $query->setFacetMinCount( 1 );


        try {
            $query_response = $this->client->query($query);
            if(!$query_response->success()) {
                return false;
            }
            $response = $query_response->getResponse();
        } catch (Exception $e) {
            pr($e->getMessage()); die;
            //CakeLog::write('solr', 'options: '.var_export($this->client->getOptions(),true).', Message: '.$e->getMessage());
            //return false;
        }
        var_dump($query->getFacet());
        pr($response);
        die;
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
	}*/
}
?>