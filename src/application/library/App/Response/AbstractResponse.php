<?php

namespace App\Response;

use \Phalcon\DI\Injectable as Injectable;

abstract class AbstractResponse extends Injectable
{
    /**
     * @var bool Is HEAD request method?
     */
    protected $head = false;

    public function __construct(\Phalcon\DiInterface $di = null)
    {
        if (null === $di) {
            $this->setDI(\Phalcon\DI::getDefault());
        } else {
            $this->setDI($di);
        }

        if ($this->di->get('request')->isHead()) {
            $this->head = true;
        }
    }

    /**
     * In-Place, recursive conversion of array keys in snake_Case to camelCase
     * @param array $data Array with snake_keys
     * @return array
     */
    protected function arrayKeysToSnake($data)
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = $this->arrayKeysToSnake($v);
            }

            $data[$this->snakeToCamel($k)] = $v;
            if ($this->snakeToCamel($k) != $k) {
                unset($data[$k]);
            }
        }
        return $data;
    }

    /**
     * Replaces underscores with spaces, uppercases the first letters of each word,
     * lowercases the very first letter, then strips the spaces
     * @param string $value String to be converted
     * @return string Converted string
     */
    protected function snakeToCamel($value)
    {
        return str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $value))));
    }
}
