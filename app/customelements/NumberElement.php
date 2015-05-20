<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 4/05/15
 * Time: 01:07 PM
 */
namespace SDT\Elements;

use SDT\Tags\MyTags;
use Phalcon\Forms\Element;

class NumberElement extends Element
{
    public function render($attributes=null)
    {
        //Merge the attributes passed in the constructor/setters with the ones here
        $attributes = $this->prepareAttributes($attributes);
        return MyTags::numberField($attributes);
    }
}