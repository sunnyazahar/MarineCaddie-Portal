<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrrDocument extends Model
{
    protected $fillable = [
        'crr_id',
        'file_name',
        'file_path',
        'file_type',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    public function crr()
    {
        return $this->belongsTo(Crr::class);
    }

    public function fileUrl(): string
    {
        return route('stocks.documents.show', [$this->crr_id, $this->id], false);
    }

    public static function fileTypeOptions(): array
    {
        $options = array_unique(array_merge(
            ShipmentDocument::fileTypeOptions(),
            [
                'CIPL',
                'Customs Doc',
                'Fumigation certificate',
                'Label',
                'Other',
                'PU Label',
            ]
        ));

        $options = array_values(array_filter(
            $options,
            static fn ($type) => $type !== 'Unspecified'
        ));

        natcasesort($options);
        $options = array_values($options);
        $options[] = 'Unspecified';

        return $options;
    }

    public static function fileTypeOptionsWithCustom(): array
    {
        $defaults = self::fileTypeOptions();

        $custom = self::query()
            ->select('file_type')
            ->distinct()
            ->whereNotNull('file_type')
            ->pluck('file_type')
            ->filter(function ($type) use ($defaults) {
                return $type !== '' && ! in_array($type, $defaults, true);
            })
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        return array_merge($defaults, $custom);
    }
}
