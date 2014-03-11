<?php 

class SendMail {

    private $_to;
    private $_subject;
    private $_from;
    private $_fromName;
    private $_body;
    private $_type = 'text/plain';


    public function setTo($to) {
        $this->_to = $to;
    }

    public function setSubject($subject) {
        $this->_subject = $subject;
    }

    public function setFrom($from) {
        $this->_from = $from;
    }

    public function setFromName($fromName) {
        $this->_fromName = $fromName;
    }

    public function setBody($body) {
        $this->_body = $body;
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function send() {

        $header = "Content-Type: " . $this->_type . "; charset=UTF-8\r\n";
        $header .= "Content_Language: ja\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Transfer-Encoding: BASE64\r\n";
        $header .= "Message-Id: <" . MD5(uniqid(microtime())) . strstr($this->_from, '@') . ">\r\n";
        $header .= "Return-Path: " . $this->_from . "\r\n";
        $header .= "From:" . $this->_fromName . "<" . $this->_from . ">\r\n";
        $header .= "Sender: " . $this->_from . "\r\n";
        $header .= "Reply-To: " . $this->_from . "\r\n";
        $header .= "Organization: " . $this->_fromName . "\r\n";
        $header .= "X-Sender: " . $this->_from . "\r\n";
        $header .= "X-Priority: 3 \r\n";

        // var_dump(nl2br(htmlspecialchars($header)));

        mb_language('uni');
        mb_internal_encoding('UTF-8');

        // var_dump(nl2br(htmlspecialchars($header)));

        $res = mb_send_mail($this->_to, $this->_subject, $this->_body, mb_encode_mimeheader($header), '-f' . $this->_from);

        return $res;

    }

}