<?php

namespace App\Models;

use App\Constant\TableConstant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public  $image_path = 'images/categories/';
    protected $table = TableConstant::CATEGORY;

    protected $fillable = ['name', 'image_url', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value
                ? $this->image_path . $value
                : null,
        );
    }
}
