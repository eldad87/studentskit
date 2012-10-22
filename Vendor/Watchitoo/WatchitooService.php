<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sivan
 * Date: 10/9/12
 * Time: 12:05 AM
 * To change this template use File | Settings | File Templates.
 */

class WatchitooService {

    public function __construct() {
        Configure::load('watchitoo');
        $this->customerID = Configure::read('Watchitoo.customerId');
        $this->privateKey = Configure::read('Watchitoo.privateKey');
        $this->domain = Configure::read('Watchitoo.domain');
    }

    protected function  signature($method, $query=array()) {
        //Remove null values
        foreach($query AS $key=>$val) {
            if(is_null($val)) {
                unset($query[$key]);
            }
        }

        $APIRequest = 'API/thirdParty/'.$method.'?'.http_build_query($query);

        //Using the secure wrapper for a Customer
        $APIRequest .= '&customer_id='.$this->customerID.'&timestamp='.time().'&uuid='.String::uuid();

        //Generating the signature for the vendor secure wrapper
        $signature =  md5( $APIRequest . $this->privateKey );

        //Final request to be executed
        return $this->domain.$APIRequest.'&signature='.$signature;
    }

    protected function fetchRequest($url) {
        App::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();
        $response = $HttpSocket->get($url);
        return $response['body'];
    }



    protected function parseResponse($xmlString) {
        return Xml::toArray(Xml::build($xmlString));
    }

    public function saveUser($userId=null, $email, $password, $firstName, $lastName=null, $displayName=null) {
        if(is_null($displayName)) {
            $displayName = $firstName;
            if($lastName) {
                $displayName .= ' '.$lastName;
            }
        }

        $requestUrl = $this->signature('saveUser', array(
            'email'             =>$email,
            'login_name'        =>$email,
            'password'          =>$password,
            'first_name'        =>$firstName,
            'last_name'         =>$lastName,
            'display_full_name' =>$displayName
        ));

        $results = $this->fetchRequest($requestUrl);
        return $this->parseResponse($results);
    }
    public function saveMeeting($meetingId=null, $userId, $title, $description, $eventDate=null) {

        $save = array(
            'user_id'           =>$userId,
            'title'             =>$title,
            'description'       =>$description,
        );
        if($eventDate) {
            //Only live lessons have a datetime
            $save['event_date'] = $eventDate;
        }
        $requestUrl = $this->signature('saveMeeting', $save);
        $results = $this->fetchRequest($requestUrl);
        return $this->parseResponse($results);
    }

    public function uploadUserContentURL($meetingId=null, $title, $description, $eventDate) {

        $requestUrl = $this->signature('uploadUserContentURL', array(
            'title'             =>$title,
            'description'       =>$description,
            'event_date'        =>$eventDate,
        ));

        $results = $this->fetchRequest($requestUrl);
        return $this->parseResponse($results);
    }
}