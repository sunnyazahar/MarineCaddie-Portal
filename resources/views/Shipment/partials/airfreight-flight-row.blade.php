@php
    $rowIndex = $index ?? 0;
    $firstLabel = $rowIndex === 0 ? 'AWB number' : 'Departure point';
    $formatFlightDate = function ($value) {
        if (!$value) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d.m.Y');
        }

        return $value;
    };
@endphp
<div class="airfreight-flight-row">
    <div class="flight-field flight-first-field">
        <div class="form-group-custom mb-0">
            <label>{{ $firstLabel }}</label>
            <input type="text" name="flights[{{ $rowIndex }}][leg_reference]" class="form-control-sm-custom" value="{{ old('flights.' . $rowIndex . '.leg_reference', isset($flight) ? ($flight->leg_reference ?? '') : '') }}">
        </div>
    </div>
    <div class="flight-field">
        <div class="form-group-custom mb-0">
            <label>Flight no.</label>
            <input type="text" name="flights[{{ $rowIndex }}][flight_number]" class="form-control-sm-custom" value="{{ old('flights.' . $rowIndex . '.flight_number', isset($flight) ? ($flight->flight_number ?? '') : '') }}">
        </div>
    </div>
    <div class="flight-field">
        <div class="form-group-custom mb-0">
            <label>Departure date</label>
            <div class="input-with-icon">
                <input type="text" name="flights[{{ $rowIndex }}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('flights.' . $rowIndex . '.departure_date', isset($flight) ? $formatFlightDate($flight->departure_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="flight-field">
        <div class="form-group-custom mb-0">
            <label>Arrival date</label>
            <div class="input-with-icon">
                <input type="text" name="flights[{{ $rowIndex }}][arrival_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('flights.' . $rowIndex . '.arrival_date', isset($flight) ? $formatFlightDate($flight->arrival_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="flight-field flight-field-time">
        <div class="form-group-custom mb-0">
            <label>Arrival time</label>
            <input type="text" name="flights[{{ $rowIndex }}][arrival_time]" class="form-control-sm-custom flight-time-input" placeholder="hh:mm" value="{{ old('flights.' . $rowIndex . '.arrival_time', isset($flight) ? ($flight->arrival_time ?? '') : '') }}">
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 flight-remove-btn remove-airfreight-flight" title="Remove flight">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
