<div>
    @php
        $labelText = $label ?? ucwords(str_replace('_', ' ', $name));
    @endphp

    <div class="form-group">
        @if($labelText)
            <label for="{{ $name }}">{{ $labelText }}</label>
        @endif

        <select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }} {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }}>
            @foreach($options as $optionValue => $optionText)
                <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
                    {{ ucwords($optionText) }}
                </option>
            @endforeach
        </select>

        @error($name)
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>