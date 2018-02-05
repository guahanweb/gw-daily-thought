<?php
namespace GW\DailyThought\Resources;

class Curl {
    protected $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
    protected $url;
    protected $follow_location;
    protected $timeout;
    protected $max_redirects;
    protected $cookie_file_location = './cookie.txt';
    protected $method = 'GET';
    protected $post_fields;
    protected $referer = 'http://www.google.com';

    protected $session;
    protected $webpage;
    protected $include_header;
    protected $no_body;
    protected $status;
    protected $binary_transfer;

    protected $authentication = false;
    protected $auth_name = '';
    protected $auth_pass = '';

    public function __construct($url = '', $follow_location = true, $timeout = 30, $max_redirects = 4, $binary_transfer = false, $include_header = false, $no_body = false) {
        $this->url = $url;
        $this->follow_location = $follow_location;
        $this->timeout = $timeout;
        $this->max_redirects = $max_redirects;
        $this->no_body = $no_body;
        $this->include_header = $include_header;
        $this->binary_transfer = $binary_transfer;
        $this->cookie_file_location = dirname(__FILE__) . '/cookie.txt';
    }

    public function auth($user, $pass) {
        $this->authentication = false;
        $this->auth_name = $user;
        $this->auth_pass = $pass;
    }

    public function setPost($post_fields) {
        $this->method = 'POST';
        $this->post_fields = $post_fields;
    }

    public function createCurl($url = null) {
        if (null !== $url) {
            $this->url = $url;
        }

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $this->url);
        curl_setopt($s, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($s, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($s, CURLOPT_MAXREDIRS, $this->max_redirects);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, $this->follow_location);
        curl_setopt($s, CURLOPT_COOKIEJAR, $this->cookie_file_location);
        curl_setopt($s, CURLOPT_COOKIEFILE, $this->cookie_file_location);

        if ($this->method === 'POST') {
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $this->post_fields);
        }

        if ($this->authentication) {
            curl_setopt($s, CURLOPT_USERPWD, $this->auth_name . ':' . $this->auth_pass);
        }

        if ($this->include_header) {
            curl_setopt($s, CURLOPT_HEADER, true);
        }

        if ($this->no_body) {
            curl_setopt($s, CURLOPT_NOBODY, true);
        }

        curl_setopt($s, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($s, CURLOPT_REFERER, $this->referer);

        $this->webpage = curl_exec($s);
        $this->status = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
    }

    public function getHttpStatus() {
        return $this->status;
    }

    public function getJson() {
        return json_decode($this->webpage);
    }

    public function __tostring() {
        return $this->webpage;
    }
}
