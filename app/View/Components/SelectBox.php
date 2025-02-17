<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectBox extends Component
{
    public string $name;
    public ?string $label;
    public array $options;
    public $value;
    public bool $required;
    public bool $disabled;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name, 
        array $options = [], 
        $value = null, 
        ?string $label = null, 
        bool $required = false, 
        bool $disabled = false
    ) {
        $this->name = $name;
        $this->options = $options;
        $this->value = $value;
        $this->label = $label;
        $this->required = $required;
        $this->disabled = $disabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-box');
    }
}
