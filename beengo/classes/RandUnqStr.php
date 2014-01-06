<?php

class RandUnqStr extends ManageDB {

    private $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private $figures = 4;

    private function generateRandStr() {
        $max = strlen($this->chars) - 1;
        $str = '';
        for ($i = 0; $i < $this->figures; $i++) {
            $str .= $this->chars[mt_rand(0, $max)];
        }
        return $str;
    }

    private function checkExistence($str) {
        $sql = 'select address from events where address = "' . $str . '"';
        $stmt = $this->dbh->query($sql);
        $data = $stmt->fetch();
        $this->dbh = null;
        return $data ? true : false;
    }

    public function getStr() {
        do {
            $str = $this->generateRandStr();
            $res = $this->checkExistence($str);   
        } while ($res);
        return $str;
    }

}