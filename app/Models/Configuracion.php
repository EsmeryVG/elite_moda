<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    public static function get(string $clave, $default = null)
    {
        return self::where('clave', $clave)->first()?->valor ?? $default;
    }

    public static function set(string $clave, string $valor): void
    {
        self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor]
        );
    }
}