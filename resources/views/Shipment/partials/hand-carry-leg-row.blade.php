@php
    $rowIndex = $index ?? 0;
    $formatLegDate = function ($value) {
        if (!$value) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d.m.Y');
        }

        return $value;
    };
    $onboardChecked = old(
        'hand_carry_legs.' . $rowIndex . '.onboard_hand_carry',
        isset($leg) ? (bool) ($leg->onboard_hand_carry ?? false) : false
    );
@endphp
<div class="hand-carry-leg-row">
    <div class="hand-carry-leg-field">
        <div class="form-group-custom mb-0">
            <label>Departure date</label>
            <div class="input-with-icon">
                <input type="text" name="hand_carry_legs[{{ $rowIndex }}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('hand_carry_legs.' . $rowIndex . '.departure_date', isset($leg) ? $formatLegDate($leg->departure_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="hand-carry-leg-field">
        <div class="form-group-custom mb-0">
            <label>Arrival date</label>
            <div class="input-with-icon">
                <input type="text" name="hand_carry_legs[{{ $rowIndex }}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('hand_carry_legs.' . $rowIndex . '.arrival_date', isset($leg) ? $formatLegDate($leg->arrival_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="hand-carry-leg-field hand-carry-leg-field-time">
        <div class="form-group-custom mb-0">
            <label>Arrival time</label>
            <input type="text" name="hand_carry_legs[{{ $rowIndex }}][arrival_time]" class="form-control-sm-custom hand-carry-leg-time-input" placeholder="hh:mm" value="{{ old('hand_carry_legs.' . $rowIndex . '.arrival_time', isset($leg) ? ($leg->arrival_time ?? '') : '') }}">
        </div>
    </div>
    <div class="hand-carry-leg-field">
        <div class="form-group-custom mb-0">
            <label>Courier contact</label>
            <input type="text" name="hand_carry_legs[{{ $rowIndex }}][contact_name]" class="form-control-sm-custom" value="{{ old('hand_carry_legs.' . $rowIndex . '.contact_name', isset($leg) ? ($leg->contact_name ?? '') : '') }}">
        </div>
    </div>
    <div class="hand-carry-leg-field">
        <div class="form-group-custom mb-0">
            <label>Courier phone</label>
            <input type="text" name="hand_carry_legs[{{ $rowIndex }}][contact_phone]" class="form-control-sm-custom" value="{{ old('hand_carry_legs.' . $rowIndex . '.contact_phone', isset($leg) ? ($leg->contact_phone ?? '') : '') }}">
        </div>
    </div>
    <div class="hand-carry-leg-field hand-carry-leg-checkbox">
        <div class="checkbox-fade fade-in-primary mb-0" style="padding-bottom: 6px;">
            <label class="mb-0 d-flex align-items-center" style="white-space: nowrap;">
                <input type="checkbox" name="hand_carry_legs[{{ $rowIndex }}][onboard_hand_carry]" value="1" {{ $onboardChecked ? 'checked' : '' }}>
                <span class="cr"><i class="cr-icon ti-check txt-primary"></i></span>
                <span class="text-inverse" style="font-size: 10px;">Onboard courier</span>
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 hand-carry-leg-remove-btn remove-hand-carry-leg" title="Remove">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
