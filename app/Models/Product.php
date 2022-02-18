<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'price',
        'description',
        'product_categories_id',
        'tags',
    ];

    public function product_categories(){
        return $this->belongsTo(ProductCategory::class, 'product_categories_id', 'id');
    } 
    
    public function product_galleries(){
        return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    } 
}
