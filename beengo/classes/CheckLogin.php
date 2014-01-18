<?php 

// --------------------------------------------------
// 概　要 : address（eventに割り当てられたランダムなユニーク文字列）からevent_idを取得
// 引数：$eventId;
// 更　新 : 131209
// --------------------------------------------------

class CheckLogin extends ManageDB {

    private $eventId;

    public function __construct($eventId) {
        parent::__construct();
        $this->eventId = $eventId;
    }

    public function checkPass($pass) {
        $where = 'event_id = ' . $this->eventId . ' && pass = "' . $pass . '"';
        $res = $this->select('event_id', 'events', $where);
        $res = $res->fetch();
        return !empty($res) ? true : false;
    }

    public function checkMasterPass($master_pass) {
        $where = 'event_id = ' . $this->eventId . ' && master_pass = "' . $master_pass . '"';
        $res = $this->select('event_id', 'events', $where);
        $res = $res->fetch();
        return !empty($res) ? true : false;
    }

    public function checkLogin() {
        if (!($_SESSION['login'] == 'ok' && $this->checkPass($_SESSION['pass']) == true)) {
            header('Location: ' . SITE_URL . 'login?address=' . $_SESSION['address']);
        }
    }

    public function checkMasterLogin() {
        if (!($_SESSION['master_login'] == 'ok' && $this->checkMasterPass($_SESSION['master_pass']) == true)) {
            header('Location: ' . SITE_URL . 'master_login?address=' . $_SESSION['address']);
        }
    }

}