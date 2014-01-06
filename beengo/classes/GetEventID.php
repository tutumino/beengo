<?php 

// --------------------------------------------------
// 概　要 : address（eventに割り当てられたランダムなユニーク文字列）からevent_idを取得
// 更　新 : 131125
// --------------------------------------------------

class GetEventID extends ManageDB {

    public function __construct() {
        parent::__construct();
    }

    public function get($address) {
        // var_dump($address);
        $where = 'address = :address';
        $res = $this->select('event_id', 'events', $where, array('address' => $address));
        $res = $res->fetch();
        $res = $res[0];
        return $res;
    }

}