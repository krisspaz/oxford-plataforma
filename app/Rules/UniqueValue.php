<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueValue implements Rule
{
    protected $table;
    protected $column;
    protected $ignoreId;

    public function __construct($table, $column, $ignoreId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)->where($this->column, $value);

        if ($this->ignoreId) {
            $query->where('id', '<>', $this->ignoreId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return 'El valor ingresado ya está registrado. Por favor, ingrese otro valor.';
    }
}
