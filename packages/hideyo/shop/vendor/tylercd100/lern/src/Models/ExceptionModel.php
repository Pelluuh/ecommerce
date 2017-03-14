<?php

namespace Tylercd100\LERN\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionModel extends Model {
    protected $table;
    protected $guarded = array('id');
    
    protected $casts = array(
        'data' => 'array'
    );
    
    public function __construct(array $attributes = [])
    {
        $this->table = config('lern.record.table');
        parent::__construct($attributes);
    }
}
