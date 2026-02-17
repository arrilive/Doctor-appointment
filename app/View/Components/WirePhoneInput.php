<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WirePhoneInput extends Component
{
    public $label;
    public $name;
    public $value;
    public $placeholder;

    public function __construct($label = null, $name, $value = null, $placeholder = null)
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.wire-phone-input');
    }
}