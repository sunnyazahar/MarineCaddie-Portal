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
@endphp
<div class="courier-leg-row">
    <div class="courier-leg-field">
        <div class="form-group-custom mb-0">
            <label>Tracking number</label>
            <input type="text" name="courier_legs[{{ $rowIndex }}][airway_bill]" class="form-control-sm-custom" value="{{ old('courier_legs.' . $rowIndex . '.airway_bill', isset($leg) ? ($leg->airway_bill ?? '') : '') }}">
        </div>
    </div>
    <div class="courier-leg-field">
        <div class="form-group-custom mb-0">
            <label>Courier name</label>
            <input type="text" name="courier_legs[{{ $rowIndex }}][carrier]" class="form-control-sm-custom" value="{{ old('courier_legs.' . $rowIndex . '.carrier', isset($leg) ? ($leg->carrier ?? '') : '') }}">
        </div>
    </div>
    <div class="courier-leg-field">
        <div class="form-group-custom mb-0">
            <label>Departure date</label>
            <div class="input-with-icon">
                <input type="text" name="courier_legs[{{ $rowIndex }}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('courier_legs.' . $rowIndex . '.departure_date', isset($leg) ? $formatLegDate($leg->departure_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="courier-leg-field">
        <div class="form-group-custom mb-0">
            <label>Arrival date</label>
            <div class="input-with-icon">
                <input type="text" name="courier_legs[{{ $rowIndex }}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('courier_legs.' . $rowIndex . '.arrival_date', isset($leg) ? $formatLegDate($leg->arrival_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="courier-leg-field courier-leg-field-time">
        <div class="form-group-custom mb-0">
            <label>Arrival time</label>
            <input type="text" name="courier_legs[{{ $rowIndex }}][arrival_time]" class="form-control-sm-custom courier-leg-time-input" placeholder="hh:mm" value="{{ old('courier_legs.' . $rowIndex . '.arrival_time', isset($leg) ? ($leg->arrival_time ?? '') : '') }}">
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 courier-leg-remove-btn remove-courier-leg" title="Remove">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
