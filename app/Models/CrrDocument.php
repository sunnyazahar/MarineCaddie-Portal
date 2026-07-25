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
        return [
            'CIPL',
            'Customs Doc',
            'DG docs',
            'Fumigation certificate',
            'Label',
            'MSDS',
            'Other',
            'Picture',
            'PO',
            'PU Label',
            'Unspecified',
        ];
    }
}
