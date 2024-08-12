<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputBox extends Component
{
    /**
     * Create a new component instance.
     */
    public $type;
    public $name;
    public $value;
    public $placeholder;
    public $required;
    public $disabled;
    public $label;
    public function __construct($type = 'text', $name = '', $value =NULL, $placeholder = '',$required=false,$disabled=false,$label=NULL)
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->label = $label;


    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-box');
    }
}
