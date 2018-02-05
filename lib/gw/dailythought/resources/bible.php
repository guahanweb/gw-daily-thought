<?php
namespace GW\DailyThought\Resources;

class Bible {
    static public function getInstance() {
        static $instance;
        if (null === $instance) {
            $instance = new Bible();
            $instance->api = Api::getInstance();
        }
        return $instance;
    }

    public function test() {
        $versions = $this->api->versions();
        var_dump($versions);
    }
}
