<?php

// --------------------------------------------------
// 概　要 : イベント操作関連
// 更　新 : 131125
// --------------------------------------------------

class CreateEvent extends ManageDB {

    public function __construct() {
        parent::__construct();
    }

    // --------------------------------------------------
    // 概要：イベントを作成（eventsテーブルに情報をinsert）
    // 戻り値：event_id
    // 引数：title、description、required_time、address、pass、datetime
    // --------------------------------------------------
    public function insertEvent($title, $master_name, $description, $required_time, $address, $pass, $datetime) {

        $this->insert('events', array('title' => $title, 'master_name' => $master_name, 'description' => $description, 'required_time' => $required_time, 'address' => $address, 'pass' => $pass, 'master_pass' => $pass));
        
        $where = 'address = :address';
        $res = $this->select('event_id', 'events', $where, array('address' => $address));
        $res = $res->fetch(PDO::FETCH_NUM);
        $eventId = $res[0];
        // $eventId = $eventId[0];
        
        foreach ($datetime as $value) {
            $this->insert('datetimes', array('event_id' => $eventId, 'datetime' => $value));
        }

        // // DB接続解除
        // $this->dbh = null;

        return $eventId;
    }

}