<div class="row">
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('Line1') }}</label>
            <input type="text" class="form-control" id="line1" name="{{$type}}[line1]"
                   placeholder="{{ __('Line1') }}"
                   value="{{$dataTypeContent->{$type}->line1 ?? old("$type.line1")}}">
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('Line2') }}</label>
            <input type="text" class="form-control" id="line2" name="{{$type}}[line2]"
                   placeholder="{{ __('Line2') }}"
                   value="{{$dataTypeContent->{$type}->line2 ?? old("$type.line2")}}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('Line3') }}</label>
            <input type="text" class="form-control" id="line3" name="{{$type}}[line3]"
                   placeholder="{{ __('Line3') }}"
                   value="{{$dataTypeContent->{$type}->line3 ?? old("$type.line3")}}">
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('City') }}</label>
            <input type="text" class="form-control" id="city" name="{{$type}}[city]"
                   placeholder="{{ __('City') }}"
                   value="{{$dataTypeContent->{$type}->city ?? old("$type.city")}}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('State') }}</label>
            <input type="text" class="form-control" id="state" name="{{$type}}[state]"
                   placeholder="{{ __('State') }}"
                   value="{{$dataTypeContent->{$type}->state ?? old("$type.state")}}">
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('Postal Code') }}</label>
            <input type="text" class="form-control" id="postal_code" name="{{$type}}[postal_code]"
                   placeholder="{{ __('Postal Code') }}"
                   value="{{$dataTypeContent->{$type}->postal_code ?? old("$type.postal_code")}}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6" style="margin-bottom: 0px;">
        <div class="form-group">
            <label for="name">{{ __('Country') }}</label>

            <select name="{{$type}}[country_code]" id="country_code" class="form-control">
                <option value="MX">México</option>
            </select>
            {{--}
            <input type="text" class="form-control" id="state" name="state"
                   placeholder="{{ __('State') }}"
                   value="@if(isset($dataTypeContent->{$type})){{ $dataTypeContent->{$type}->state }}@endif">
                   --}}
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 0px;">
        <input type="hidden" name="{{$type}}[type]" value="{{$type}}">
        {{--
        <div class="form-group">
            <label for="name">{{ __('Postal Code') }}</label>
            <input type="text" class="form-control" id="postal_code" name="postal_code"
                   placeholder="{{ __('Postal Code') }}"
                   value="@if(isset($dataTypeContent->{$type})){{ $dataTypeContent->{$type}->postal_code }}@endif">
        </div>
        --}}
    </div>
</div>
