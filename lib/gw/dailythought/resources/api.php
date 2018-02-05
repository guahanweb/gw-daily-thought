<?php
namespace GW\DailyThought\Resources;

class Api {
    protected $base_url = 'https://bibles.org/v2/';
    protected $auth_user = 'nUagRnOOBLek4uMa5s9hEzFpBh6c3vruIrCysGHz';
    protected $auth_pass = 'X';
    protected $version = 'eng-KJV';

    protected $curl;

    static public function getInstance() {
        static $instance;
        if (null === $instance) {
            $instance = new Api();
        }
        return $instance;
    }

    protected function __construct() {
        $this->curl = new Curl();
        $this->curl->auth($this->auth_user, $this->auth_pass);
    }

    public function books($include_chapters = false) {
        $path = sprintf('versions/%s/books.js', $this->version);
        if ($include_chapters) {
            $path .= '?include_chapters=true';
        }

        return $this->request($path)->books;
    }

    public function bookgroups($id = null) {
        $path = 'bookgroups';
        if ($id !== null) {
            $path = sprintf('%s/%d', $path, $id);
        }
        $path .= '.js';

        return $this->request($path)->bookgroups;
    }

    public function chapters($book) {
        $path = sprintf('books/%s:%s/chapters.js', $this->version, $book);
        return $this->request($path)->chapters;
    }

    public function verses($book, $chapter) {
        $path = sprintf('chapters/%s:%s.%d/verses.js', $this->version, $book, $chapter);
        return $this->request($path)->verses;
    }

    public function versions() {
        return $this->request('versions.js')->versions;
    }

    protected function request($path, $payload = null) {
        $this->curl->createCurl($this->base_url . $path);

        $code = $this->curl->getHttpStatus();
        if ($code !== 200) {
            throw new \Exception('Failed API call', $code);
        }

        return $this->curl->getJson()->response;
    }
}
