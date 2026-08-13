@php
    $rowIndex = $index ?? 0;
    $firstLabel = $rowIndex === 0 ? 'Bill of lading' : 'Port of departure';
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
<div class="sea-freight-leg-row">
    <div class="sea-leg-field sea-leg-first-field">
        <div class="form-group-custom mb-0">
            <label>{{ $firstLabel }}</label>
            <input type="text" name="sea_legs[{{ $rowIndex }}][bill_of_lading]" class="form-control-sm-custom" value="{{ old('sea_legs.' . $rowIndex . '.bill_of_lading', isset($leg) ? ($leg->bill_of_lading ?? '') : '') }}">
        </div>
    </div>
    <div class="sea-leg-field">
        <div class="form-group-custom mb-0">
            <label>Container number</label>
            <input type="text" name="sea_legs[{{ $rowIndex }}][container_number]" class="form-control-sm-custom" value="{{ old('sea_legs.' . $rowIndex . '.container_number', isset($leg) ? ($leg->container_number ?? '') : '') }}">
        </div>
    </div>
    <div class="sea-leg-field">
        <div class="form-group-custom mb-0">
            <label>Transport vessel IMO</label>
            <input type="text" name="sea_legs[{{ $rowIndex }}][transport_vessel_imo]" class="form-control-sm-custom" value="{{ old('sea_legs.' . $rowIndex . '.transport_vessel_imo', isset($leg) ? ($leg->transport_vessel_imo ?? '') : '') }}">
        </div>
    </div>
    <div class="sea-leg-field">
        <div class="form-group-custom mb-0">
            <label>Transport vessel name</label>
            <input type="text" name="sea_legs[{{ $rowIndex }}][transport_vessel_name]" class="form-control-sm-custom" value="{{ old('sea_legs.' . $rowIndex . '.transport_vessel_name', isset($leg) ? ($leg->transport_vessel_name ?? '') : '') }}">
        </div>
    </div>
    <div class="sea-leg-field">
        <div class="form-group-custom mb-0">
            <label>ETD</label>
            <div class="input-with-icon">
                <input type="text" name="sea_legs[{{ $rowIndex }}][etd]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('sea_legs.' . $rowIndex . '.etd', isset($leg) ? $formatLegDate($leg->etd ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="sea-leg-field">
        <div class="form-group-custom mb-0">
            <label>ETA</label>
            <div class="input-with-icon">
                <input type="text" name="sea_legs[{{ $rowIndex }}][eta]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('sea_legs.' . $rowIndex . '.eta', isset($leg) ? $formatLegDate($leg->eta ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="sea-leg-field sea-leg-field-time">
        <div class="form-group-custom mb-0">
            <label>Arrival time</label>
            <input type="text" name="sea_legs[{{ $rowIndex }}][arrival_time]" class="form-control-sm-custom sea-leg-time-input" placeholder="hh:mm" value="{{ old('sea_legs.' . $rowIndex . '.arrival_time', isset($leg) ? ($leg->arrival_time ?? '') : '') }}">
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 sea-leg-remove-btn remove-sea-freight-leg" title="Remove leg">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
