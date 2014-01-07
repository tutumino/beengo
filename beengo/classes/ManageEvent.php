<?php

// --------------------------------------------------
// 概　要 : イベント操作関連
// 更　新 : 131204
// --------------------------------------------------

class ManageEvent extends ManageDB {

    private $eventId;

    public function __construct($eventId) {
        parent::__construct();
        $this->eventId = $eventId;
    }

    // --------------------------------------------------
    // 概要：イベント情報（eventsテーブル）の取得（flag_del = 1 は除く）
    // 引数：event_id
    // 戻り値：結果セット（連想配列）
    // --------------------------------------------------
    public function getEvent() {

        $fields = '*';
        $where = 'event_id = :event_id && flag_del = 0';
        // var_dump($this->eventId);
        // var_dump($eventId);
        $res = $this->select($fields, 'events', $where, array('event_id' => $this->eventId));

        return $res;
    }

    // --------------------------------------------------
    // 概要：候補日時（datetimesテーブル）の取得
    // 引数：event_id
    // 戻り値：$res（以下の配列）
    //     array(
    //         array(
    //             'datetime_id' => datetime_id,
    //             'year' => 年,
    //             'month' => 月,
    //             'date' => 日,
    //             'day' => 曜日,
    //             'time' => 時間
    //         )
    //     )
    // --------------------------------------------------
    public function getDatetimes() {

        $fields = 'datetime_id, datetime';
        $where = 'event_id = :event_id order by datetime_id';
        $temp = $this->select($fields, 'datetimes', $where, array('event_id' => $this->eventId));
        $res = array();
        foreach ($temp as $value) {
            $year = substr($value['datetime'], 0, 4);
            $month = substr($value['datetime'], 4, 2);
            $date = substr($value['datetime'], 6, 2);
            $days = array('（日）', '（月）', '（火）', '（水）', '（木）', '（金）', '（土）');
            $mktime = mktime(0, 0, 0, $month, $date, $year);
            $day = date('w', $mktime);
            $time = '';
            if (strlen($value['datetime']) == 12) {
                $time = substr($value['datetime'], 8, 2) . ':' . substr($value['datetime'], 10, 2);
            }           
            $res[] = array('datetime_id' => $value['datetime_id'], 'year' => $year, 'month' => $month, 'date' => $date, 'day' => $days[$day], 'time' => $time);
        }
        return $res;
    }

    // --------------------------------------------------
    // 概要：メンバー情報（membersテーブル）と日程可否（answersテーブル）の登録
    // 引数：menber_id、comment、datetime_id（配列）、answer（配列）
    // 戻り値：メンバーID
    // --------------------------------------------------
    public function register($member_name, $comment, array $datetime_id, array $answer) {

        $this->insert('members', array('event_id' => $this->eventId, 'member_name' => $member_name, 'comment' => $comment));
        // var_dump($datetime_id);

        $memberId = $this->getLastInsertId();
        // var_dump($memberId);

        for ($i = 0; $i < count($datetime_id); $i++) {
            $this->insert('answers', array('event_id' => $this->eventId, 'datetime_id' => $datetime_id[$i], 'member_id' => $memberId, 'answer' => $answer[$i]));
        }
        return $memberId;
    }

    // --------------------------------------------------
    // 概要：メンバー情報（membersテーブル）と日程可否情報（answersテーブル）の取得（メンバー自身用）
    // 引数：member_id
    // 戻り値：array $res（以下の配列）
    //     $res['member_name']
    //     $res['comment']
    //     $res['answer']（配列）
    // --------------------------------------------------
    public function getRegistered($memberId) {

        $res = array();

        $fields = 'member_name, comment';
        $where = 'member_id = :member_id';
        $temp = $this->select($fields, 'members', $where, array('member_id' => $memberId));
        $temp = $temp->fetch(PDO::FETCH_ASSOC);
        $res['member_name'] = $temp['member_name'];
        $res['comment'] = $temp['comment'];
        // var_dump($res);

        $fields = 'answer';
        $where = 'member_id = :member_id order by datetime_id';
        $temp = $this->select($fields, 'answers', $where, array('member_id' => $memberId));
        $i = 0;
        foreach ($temp as $value) {
            $res['answer'][$i] = $value['answer'];
            $i++;
        }
        return $res;
    }

    // --------------------------------------------------
    // 概要：イベントに日程可否を登録した全メンバーのIDを取得
    // 引数：なし
    // 戻り値：array $res（member_idを格納した配列）
    // --------------------------------------------------
    public function getMemberIDs() {

        $where = '`event_id` = :event_id';
        $temp = $this->select('member_id', 'members', $where, array('event_id' => $this->eventId));
        $res = array();
        foreach ($temp as $value) {
            $res[] = $value['member_id'];
        }
        return $res;
    }

    // --------------------------------------------------
    // 概要：イベント日時の決定
    // 引数：
    //     $fix（決定日時のID）、
    //     $description2、
    //     $map_type、
    //     $map_location
    // 戻り値：なし
    // --------------------------------------------------
    public function fix($fix, $description2, $map_type, $map_location) {

        $this->update('events', array('description2' => $description2, 'map_type' => $map_type, 'map_location' => $map_location, 'flag_fixed' => 1, 'modified' => null), ' where `event_id` = ' . $this->eventId);

        $this->update('datetimes', array('flag_fixed' => 1), ' where `event_id` = ' . $this->eventId . ' && `datetime_id` = ' . $fix);
    }

    // --------------------------------------------------
    // 概要：決定日時の取得
    // 引数：なし
    // 戻り値：$res（以下の配列）
    // 戻り値：$res（以下の配列）
    //     array(
    //         'year' => 年,
    //         'month' => 月,
    //         'date' => 日,
    //         'day' => 曜日,
    //         'time' => 時間
    //     )
    // --------------------------------------------------
    public function getFixed() {

        $where = 'event_id = :event_id && flag_fixed = 1';
        $temp = $this->select('`datetime`', '`datetimes`', $where, array('event_id' => $this->eventId));

        // var_dump($temp);
        $temp = $temp->fetch(PDO::FETCH_NUM);

        $year = substr($temp[0], 0, 4);
        $month = substr($temp[0], 4, 2);
        $date = substr($temp[0], 6, 2);
        $days = array('（日）', '（月）', '（火）', '（水）', '（木）', '（金）', '（土）');
        $mktime = mktime(0, 0, 0, $month, $date, $year);
        $day = date('w', $mktime);
        $time = '';
        if (strlen($temp[0]) == 12) {
            $time = substr($temp[0], 8, 2) . ':' . substr($temp[0], 10, 2);
        }

        $res = array('year' => $year, 'month' => $month, 'date' => $date, 'day' => $days[$day], 'time' => $time);

        return $res;
    }

}

