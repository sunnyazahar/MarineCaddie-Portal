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
<div class="truck-leg-row">
    <div class="truck-leg-field">
        <div class="form-group-custom mb-0">
            <label>CMR no.</label>
            <input type="text" name="truck_legs[{{ $rowIndex }}][cmr]" class="form-control-sm-custom" value="{{ old('truck_legs.' . $rowIndex . '.cmr', isset($leg) ? ($leg->cmr ?? '') : '') }}">
        </div>
    </div>
    <div class="truck-leg-field">
        <div class="form-group-custom mb-0">
            <label>Carrier name</label>
            <input type="text" name="truck_legs[{{ $rowIndex }}][freight_company]" class="form-control-sm-custom" value="{{ old('truck_legs.' . $rowIndex . '.freight_company', isset($leg) ? ($leg->freight_company ?? '') : '') }}">
        </div>
    </div>
    <div class="truck-leg-field">
        <div class="form-group-custom mb-0">
            <label>Departure date</label>
            <div class="input-with-icon">
                <input type="text" name="truck_legs[{{ $rowIndex }}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('truck_legs.' . $rowIndex . '.departure_date', isset($leg) ? $formatLegDate($leg->departure_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="truck-leg-field">
        <div class="form-group-custom mb-0">
            <label>Arrival date</label>
            <div class="input-with-icon">
                <input type="text" name="truck_legs[{{ $rowIndex }}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('truck_legs.' . $rowIndex . '.arrival_date', isset($leg) ? $formatLegDate($leg->arrival_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="truck-leg-field truck-leg-field-time">
        <div class="form-group-custom mb-0">
            <label>Arrival time</label>
            <input type="text" name="truck_legs[{{ $rowIndex }}][arrival_time]" class="form-control-sm-custom truck-leg-time-input" placeholder="hh:mm" value="{{ old('truck_legs.' . $rowIndex . '.arrival_time', isset($leg) ? ($leg->arrival_time ?? '') : '') }}">
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 truck-leg-remove-btn remove-truck-leg" title="Remove">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
