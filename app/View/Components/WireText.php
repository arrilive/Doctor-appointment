<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WireText extends Component
{
    public $label;
    public $name;

    public function __construct($label, $name)
    {
        $this->label = $label;
        $this->name = $name;
    }

    public function render()
    {
        return view('components.wire-text');
    }
}