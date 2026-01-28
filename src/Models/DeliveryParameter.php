<?php

declare(strict_types=1);

namespace JonatanPasso\RouteEngine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryParameter extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'value', 'description'];

    public static function getByName(string $name): ?string
    {
        return self::where('name', $name)->value('value');
    }
}
