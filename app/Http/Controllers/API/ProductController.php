<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $product_categories = $request->input('product_categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if($id){
            $product = Product::with(['product_categories','product_galleries'])->find($id);
            if($product){
                return ResponseFormatter::success(
                    $product,
                    'Data Produk Berhasil Diambil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Produk Tidak Ada',
                    404
                );
            }
        }
        $product = Product::with(['product_categories','product_galleries']);
        if($name){
           $product->where('name','like','%'.$name.'%');
        }   
        if($description){
           $product->where('description','like','%'.$description.'%');
        }   
        if($tags){
           $product->where('tags','like','%'.$tags.'%');
        }  
        if($price_from){
           $product->where('price','=>', $price_from);
        }  
        if($price_to){
           $product->where('price','<=', $price_from);
        }  
        if($product_categories){
           $product->where('product_categories','=', $product_categories);
        }
        
        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data Produk Berhasil Diambil'
        );
    }   
}
