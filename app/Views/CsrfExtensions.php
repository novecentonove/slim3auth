<?php


namespace App\Views;

use Slim\Http\Uri;

class CsrfExtensions extends \Twig\Extension\AbstractExtension
{
    protected $csrf;

    public function __construct($csrf){
        $this->csrf = $csrf;
    }

    public function getFunctions(){
        return [
            new \Twig\TwigFunction('csrf', array($this, 'csrf')),
        ];
    }

    public function csrf(){
       
        $nameKey = $this->csrf->getTokenNameKey();
        $name = $this->csrf->getTokenName();
        $valueKey = $this->csrf->getTokenValueKey();
        $value = $this->csrf->getTokenValue();

        return 
        '
        <input type="hidden" name="'.$nameKey.'" value="'.$name.'">
        <input type="hidden" name="'.$valueKey.'" value="'.$value.'">
        ';
    }
}
