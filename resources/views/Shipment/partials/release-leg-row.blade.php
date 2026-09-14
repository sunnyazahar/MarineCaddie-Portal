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
<div class="release-leg-row">
    <div class="release-leg-field">
        <div class="form-group-custom mb-0">
            <label>Release carrier</label>
            <input type="text" name="release_legs[{{ $rowIndex }}][freight_company]" class="form-control-sm-custom" value="{{ old('release_legs.' . $rowIndex . '.freight_company', isset($leg) ? ($leg->freight_company ?? '') : '') }}">
        </div>
    </div>
    <div class="release-leg-field">
        <div class="form-group-custom mb-0">
            <label>Delivery date</label>
            <div class="input-with-icon">
                <input type="text" name="release_legs[{{ $rowIndex }}][delivery_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('release_legs.' . $rowIndex . '.delivery_date', isset($leg) ? $formatLegDate($leg->delivery_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="release-leg-field release-leg-field-time">
        <div class="form-group-custom mb-0">
            <label>Delivery time</label>
            <input type="text" name="release_legs[{{ $rowIndex }}][delivery_time]" class="form-control-sm-custom release-leg-time-input" placeholder="hh:mm" value="{{ old('release_legs.' . $rowIndex . '.delivery_time', isset($leg) ? ($leg->delivery_time ?? '') : '') }}">
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 release-leg-remove-btn remove-release-leg" title="Remove">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
