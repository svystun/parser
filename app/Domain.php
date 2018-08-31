<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domain';
    public $timestamps = false;
    protected $fillable = ['domain', 'parent_domain_id', 'ip'];
}
