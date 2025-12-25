<?php

namespace App\Models;

use App\Enums\ConvertingSchemaType;
use App\Enums\InstanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property ConvertingSchemaType $schema_type
 * @property InstanceStatus $status
 * @property string $original_file_path
 * @property array $reading_settings
 * @property string $converted_file_path
 * @property array $converting_settings
 */
class Instance extends Model
{
    use SoftDeletes;

    protected $table = 'instances';

    protected $fillable = [
        'schema_type',
        'status',
        'original_file_path',
        'reading_settings',
        'converted_file_path',
        'converting_settings',
    ];

    protected $casts = [
        'status' => InstanceStatus::class,
        'schema_type' => ConvertingSchemaType::class,
        'reading_settings' => 'array',
        'converting_settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'reading_settings' => '[]',
        'converting_settings' => '[]',
    ];
}