<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit',5);
        $status = $request->input('status');

        if($id){
            $transaction = Transaction::with(['transaction_items.products'])->find($id);
            if($transaction){
                return ResponseFormatter::success(
                    $transaction,
                    'Data Transaksi Berhasil Diambil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Transaksi Tidak Ada',
                    404
                );
            }
        }
        $transaction = Transaction::with(['transaction_items.products'])->where('users_id', Auth::user()->id);
        if($status){
            $transaction->where('status',$status);
        }

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data Transaksi Berhasil Diambil'
        );
    }

    public function checkout(Request $request){
        $request->validate([
            'transaction_items'=>'required|array',
            'transaction_items.*.id'=> 'exists:products,id',
            'total_price' => 'required',
            'total_shipping' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED'
        ]);

        $transaction = Transaction::create([
            'users_id'=>Auth::user()->id,
            'address'=>$request->address,
            'total_price'=>$request->total_price,
            'total_shipping'=>$request->total_shipping,
            'status'=>$request->status,
        ]);

        foreach($request->transaction_items as $products){
            TransactionItem::create([
                'users_id'=>Auth::user()->id,
                'products_id'=> $products['id'],
                'transaction_id' => $transaction->id,
                'quantity'=> $products['quantity']
            ]);
        }

        return ResponseFormatter::success($transaction->load('transaction_items.products'),'Transaksi Berhasil');
    }
}
