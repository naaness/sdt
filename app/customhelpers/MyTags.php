<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 4/05/15
 * Time: 12:36 PM
 */
namespace SDT\Tags;

class MyTags extends \Phalcon\Tag
{

    /**
     * Generates a widget to show a HTML5 input[type="number"] tag
     *
     * @param array
     * @return string
     */
    static public function numberField($parameters)
    {
        return self::_inputField('number', $parameters);
    }

}