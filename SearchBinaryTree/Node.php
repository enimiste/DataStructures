<?php

/**
 * by Adrian Statescu <adrian@thinkphp.ro>
 * Twitter: @thinkphp
 * G+ : http://gplus.to/thinkphp
 * MIT Style License
 */
class Node {

    public $info;
    public $left;
    public $right;
    public $level;

    public function __construct($info) {
        $this->info = $info;
        $this->left = NULL;
        $this->right = NULL;
        $this->level = NULL;
    }

    public function __toString() {

        return "$this->info";
    }

}

?>