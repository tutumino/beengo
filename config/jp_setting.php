<?php

// 日本語のための設定を明示
// mb_language('Japanese');
mb_language('uni');
ini_set('mbstring.detect_order', 'auto');
ini_set('mbstring.http_input', 'auto');
ini_set('mbstring.http_output', 'pass');
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.script_encoding', 'UTF-8');
ini_set('mbstring.substitute_character', 'none');
mb_regex_encoding('UTF-8');
date_default_timezone_set('Asia/Tokyo'); // これがないと、時間系関数を使った時におかしくなる。