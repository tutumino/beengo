<?php

// --------------------------------------------------
// 概　要 : DB操作関連
// 更　新 : 131204
// --------------------------------------------------

class ManageDB {

    protected $dbh;

    public function __construct() {
        $this->connect();
    }

    // --------------------------------------------------
    // 概要：DB接続
    // --------------------------------------------------
    private function connect() {
        try {
            $this->dbh = new PDO(DSN, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            exit('データベースに接続できませんでした。' . $e->getMessage());
        }
    }

    // --------------------------------------------------
    // 概要：DB接続解除
    // --------------------------------------------------
    public function close() {
        $this->dbh = null;
    }

    // --------------------------------------------------
    // 概要：SELECTを実行
    // 戻り値：結果セット
    // 引数：
    //     $fields　selectするカラム名（カンマ区切りで羅列）
    //     $table　テーブル名
    //     $$where　where句（「where」を含む）
    //     $params　省略可。bindValueする時のプレースホルダとその値を、
    //         連想配列で（「:」はいらない）。省略した場合はbindValueしない。
    // --------------------------------------------------
    public function select($fields, $table, $where, array $params = null) {
        // var_dump($this->dbh);
        $sql = sprintf("select %s from %s where %s", $fields, $table, $where);
        // var_dump($sql);
        // var_dump($this->dbh);
        $stmt = $this->dbh->prepare($sql);
        // var_dump($sql);
        // var_dump($stmt);
        if ($params != null) {
            foreach ($params as $key => $value) {
                $param = ':' . $key;
                $stmt->bindValue($param, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt;
        // return $res;
    }

    // --------------------------------------------------
    // 概要：直近にinsertしたレコードのID（auto_increment値）を取得
    // 戻り値：ID
    // 引数：なし
    // --------------------------------------------------
    public function getLastInsertId() {
        $sql = 'select LAST_INSERT_ID()';
        $stmt = $this->dbh->query($sql);
        $res = $stmt->fetch(PDO::FETCH_NUM);
        return $res[0];
    }

    // --------------------------------------------------
    // 概要：INSERTを実行
    // 戻り値：なし
    // 引数：
    // $table　テーブル名
    // $data　insertするカラム名と値を連想配列で
    // --------------------------------------------------
    public function insert($table, array $data) {
        $fields = array();
        $values = array();
        foreach ($data as $key => $value) {
            $fields[] = $key;
            $values[] = ':' . $key;
        }
        $sql = sprintf("insert into %s (%s) values (%s)", $table, implode(', ', $fields), implode(', ', $values));
        $stmt = $this->dbh->prepare($sql);
        foreach ($data as $key => $value) {
            $param = ':' . $key;
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
    }

    // --------------------------------------------------
    // 概要：UPDATEを実行
    // 引数：
    //     $table　テーブル名
    //     $data　updateするカラム名と値を連想配列で
    //     $where　where句（「where」を含む）
    // 戻り値：なし
    // --------------------------------------------------
    public function update($table, array $data, $where) {
        $fieldsValues = array();
        foreach ($data as $key => $value) {
            $fieldsValues[] = $key . '=:' . $key;
        }
        // var_dump($fieldsValues);
        // var_dump($set);
        $sql = sprintf("update %s set %s $where", $table, implode(', ', $fieldsValues));
        // var_dump($sql);
        $stmt = $this->dbh->prepare($sql);
        foreach ($data as $key => $value) {
            $param = ':' . $key;
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        // var_dump($stmt);
    }

}
