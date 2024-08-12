<div>
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
        @if($string)
            <label for="{{ $name }}">{{$string}}</label>
        @endif
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="form-control"
        >
            @foreach($options as $optionValue => $optionText)
                <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
                    {{ $optionText }}
                </option>
            @endforeach
        </select>
        @error($name)
            <span class="text-danger">{{$message}}</span>
        @enderror
    </div>

</div>
