<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
    @php
        if(!isset($label))
        {
            $string = str_replace('_', ' ', $name);
            $string = ucwords($string);
        }
        else
        {
            $string=$label;
        }
    @endphp
    <div class="form-group">
        <label for="name" class="form-label @if($type=='hidden') d-none @endif" >{!! $string !!}</label>
        <input type="{{$type}}" name="{{$name}}" id="{{$name}}" class="form-control" value="{{old($name,$value)}}"
        @if(isset($disabled) and $disabled==true) disabled @endif
        @if(isset($required) and $required==true) required @endif
        >
        @error($name)
            <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
</div>
