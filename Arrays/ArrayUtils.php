<?php

class ArrayUtils
{

    /**
     *
     * @param array $arr
     * @param $map_func function($k, $v){ return array($k=>);}
     * @param callable $filter_func function($v){return false/true;}
     * @throws Exception
     * @internal param Callable $func function($key, $value) returns array($key=>$value)
     * @return array
     */
    public static function array_kmap(array $arr, $map_func, $filter_func = null)
    {
        if (!is_callable($map_func))
            throw new Exception('La fonction map_fun doit être un callable');

        $filter_func_is_callable = is_callable($filter_func);
        $r = array();
        foreach ($arr as $key => $value) {
            $a = call_user_func($map_func, $key, $value);
            if ($filter_func_is_callable AND call_user_func($filter_func, $a) === false) {
                continue;
            }
            $a_key = array_keys($a);
            $k = array_shift($a_key);
            $r[$k] = $a[$k];
        }
        return $r;
    }

    /**
     *
     * @param array $arr
     * @return array
     */
    public static function array_odd(array $arr)
    {
        $odd = array();
        foreach ($arr as $k => $v) {
            if ($k % 2 != 0) {
                $odd[] = $v;
            }
        }
        return $odd;
    }

    /**
     *
     * @param array $arr
     * @return array
     */
    public static function array_even(array $arr)
    {
        $even = array();
        foreach ($arr as $k => $v) {
            if ($k % 2 == 0) {
                $even[] = $v;
            }
        }
        return $even;
    }

    /**
     *
     * @param array $arr
     * @param Callable $pred function($val){return TRUE/FALSE;}
     * @return boolean
     */
    public static function array_fexists(array $arr, $pred)
    {
        foreach ($arr as $value) {
            $r = $pred($value);
            if ($r)
                return TRUE;
        }
        return false;
    }

    /**
     * Concat arrays
     * @param array $arr1
     * @param array $arr_ liste of arrays to concat
     * @return array
     */
    public static function array_concat(array $arr1, array $arr_ = NULL)
    {
        $r = array();
        $arrays = func_get_args();
        foreach ($arrays as $value) {
            foreach ($value as $v) {
                $r[] = $v;
            }
        }
        return $r;
    }

    /**
     * Get a value from ana array in the range position
     * @param array $arr
     * @param integer $range > 0
     * @return mixed
     */
    public static function array_value_at(array $arr, $range)
    {
        if (!is_numeric($range) || $range < 0 || count($arr) < $range)
            return NULL;
        $i = 1;
        foreach ($arr as $value) {
            if ($i == $range)
                return $value;
            else
                $i++;
        }
        return NULL;
    }

    /**
     *
     * @param array $arr
     * @param mixed $value
     * @param integer $range > 0
     * @return array
     */
    public static function array_insert_at(array $arr, $value, $range)
    {
        list($left, $right) = self::array_split($arr, $range);
        return self::array_concat($left, array($value), $right);
    }

    /**
     *
     * @param array $arr
     * @param intger $range > 0
     * @return array
     */
    public static function array_split(array $arr, $range)
    {
        if ($range <= 0)
            return array(array(), $arr);
        elseif ($range > count($arr))
            return array($arr, array());
        else {
            $left = array();
            $right = array();
            $i = 1;
            foreach ($arr as $value) {
                if ($i < $range)
                    $left[] = $value;
                else
                    $right[] = $value;
                $i++;
            }
            return array($left, $right);
        }
    }

    /**
     * @param array $data1
     * @param array $data2
     * @return array
     * @throws Exception
     */
    public static function array_kmerge_recursive(array $data1, array $data2)
    {
        if (func_num_args() <= 0)
            throw new \Exception('Vous devez passer des arguments à la fonction ' . __FUNCTION__);
        $arrays = array_filter(func_get_args(), function ($item) {
            return is_array($item);
        });
        if (count($arrays) != func_num_args())
            throw new \Exception('Les paramètres de la fonction ' . __FUNCTION__ . ' doivent être des tableaux');
        $kmerge_func = function (array $init_data, array $array_of_array_data) use (&$kmerge_func) {
            if (empty($array_of_array_data)) return $init_data;
            $data2 = array_shift($array_of_array_data);
            $d = ArrayUtils::array_kmap($init_data, function ($k, $v) use ($data2) {
                $vv = $v;
                if (array_key_exists($k, $data2)) {
                    $v2 = $data2[$k];
                    if (!is_array($v2)) $vv = $v2;
                    elseif (is_array($v)) $vv = self::array_kmerge_recursive($v, $v2);
                    else $vv = $v2;
                }
                return array($k => $vv);
            });

            foreach ($data2 as $key => $value) {
                if (!array_key_exists($key, $d)) $d[$key] = $value;
            }
            return $kmerge_func($d, $array_of_array_data);
        };

        return $kmerge_func(array_shift($arrays), $arrays);
    }

    /**
     * Retourne la valeur associée à la cléf $key dans le tableau $arr. Une exception est levée si la clés n'existe pas
     *
     * @param string $key
     * @param array $arr
     * @param bool $doted_key_notation
     * @return mixed
     * @throws \LogicException if $key dose not exists
     */
    public static function array_get_value($key, array $arr, $doted_key_notation = false)
    {
        if (!is_string($key))
            throw new \LogicException('The key should be a stirng');

        if (!$doted_key_notation OR strpos($key, '.', 0) === false) {
            if (!array_key_exists($key, $arr))
                throw new \LogicException('The key ' . $key . ' dose not exists.');
            return $arr[$key];
        }
        $key_parts = explode('.', $key);
        $key_exists = true;
        $i = 0;
        while ($i < count($key_parts) AND $key_exists) {
            $k = $key_parts[$i];
            if (is_array($arr) AND array_key_exists($k, $arr)) $arr = $arr[$k];
            else $key_exists = false;
            $i++;
        }
        if (!$key_exists)
            throw new \LogicException('The key ' . $key . ' dose not exists.');
        else return $arr;
    }

    /**
     * Tester si la clé existe dans le tableau ou non
     * La clé peut être exprimé à l'aide la notation suivante :
     * key1.key2 signifier $arr['key1']['key2']
     *
     * @param string $key
     * @param array $arr
     * @param bool $doted_key_notation use key doted notation or not
     * @return bool
     */
    public static function array_key_exists($key, array $arr, $doted_key_notation = false)
    {
        try {
            static::array_get_value($key, $arr, $doted_key_notation);
            return true;
        } catch (\LogicException $e) {
            return false;
        }
    }

    /**
     * Permet de retourner le résultat de la fonction $func appliquée à la valeur du $key dans le tableau $arr
     *
     * @param string $key
     * @param array $arr
     * @param callable $func function($value){return;}
     * @param bool $doted_key_notation
     * @return mixed
     * @throws \LogicException
     */
    public static function array_value_apply($key, array $arr, $func, $doted_key_notation = false)
    {
        if (!is_callable($func))
            throw new \LogicException('The function $func param should be callable');

        $v = static::array_get_value($key, $arr, $doted_key_notation);
        return $func($v);
    }

    /**
     * Return an array of $count sub element of the $arr param centred to the $centred_value
     * If $centred_value dose not exists in the $arr a slice of the array is returned, starting from the beginning
     *
     * @param $center_value
     * @param $count
     * @return array
     */
    public static function array_values_centred(array $arr, $center_value, $count)
    {
        if(!is_integer($count) OR $count < 0)
            throw new \LogicException('Invalid param $count, should be a positif integer');
        $arr_count = count($arr);
        if($count >= $arr_count) return $arr;

        $pos = self::array_index_of_value($arr, $center_value);
        if($pos < 0) $pos = 0;
        $left = (int)($count/2);

        $offset = $pos - $left;
        if($offset < 0) $offset = 0;

        $diff = $arr_count - ($offset + $count);
        if($diff < 0) $offset -= abs($diff);
        return array_slice($arr, $offset, $count, true);
    }

    /**
     * Return the 0 based index of the $value in the array $arr
     * -1 if dose not exists
     *
     * @param array $arr
     * @param $value
     * @return int
     */
    public  static function array_index_of_value(array $arr, $value){
        $pos = 0;
        foreach($arr as $key => $v){
            if($value == $v) return $pos;
            else $pos++;
        }
        return -1;
    }
}

?>
