<?php
/**
 * Created by PhpStorm.
 * User: elbachirnouni
 * Date: 08/03/15
 * Time: 22:12
 */

namespace Nouni\String\Utils;

/**
 * Class StringBuilder
 * @package Nouni\String\Utils
 */
class StringBuilder {

    /**
     * @var string
     */
    private $str;

    public function __construct($str = ''){
        $this->str = $this->toString($str);
    }

    /**
     * @param $str
     * @return $this
     */
    public function append($str) {
        $this->str .= $this->toString($str);
        return $this;
    }

    /**
     * @param $str
     * @return string
     */
    private function toString($str) {
        return $str;
    }

    public function __toString(){
        return $this->str;
    }
} 