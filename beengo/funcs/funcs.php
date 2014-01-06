<?php

// require_once('../../config/config.php');

// --------------------------------------------------
// 処  理 : DBに接続
// 引  数 : なし
// 戻り値 : DBハンドラ
// 更　新 : 2013/06/20
// --------------------------------------------------
function connectDb() {
    try {
        return new PDO(DSN, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        exit('データベースに接続できませんでした。' . $e->getMessage());
    }
}

// --------------------------------------------------
// 処  理 : エスケープ処理
// 引  数 : $s（エスケープしたい文字列）
// 戻り値 : エスケープ処理された文字列
// 更　新 : 2013/06/20
// --------------------------------------------------
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}