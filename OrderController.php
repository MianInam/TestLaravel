<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderResourceCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = auth()->user()->orders()->select('id', 'order_price', 'order_status')->with(['ordersDetail'])->get();
            $orders = new OrderResourceCollection($orders);
            return $this->sendJson(true, "All Orders", $orders);
        } catch (\Throwable $th) {
            Log::info("Get All Orders");
            Log::info($th->getMessage());
            return $this->sendJson(false, "Something went wrong");
        }
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = Order::create([
                "user_id" => auth()->user()->id,
                "order_price" => $request->order_price
            ]);
            foreach ($request->products as $key => $product) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_name' => $product['product_name'],
                    'product_price' => $product['product_price'],
                    'product_quantity' => $product['product_quantity'],
                ]);
            }
            DB::commit();
            return $this->sendJson(true, "Order Placed Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info("Order Creation");
            Log::info($th->getMessage());
            return $this->sendJson(false, "Something went wrong");
        }
    }

    public function update($id, Request $request)
    {
        try {
            $user_id = auth()->user()->id;
            $order = Order::where('user_id', $user_id)->where('id', $id)->first();
            if (empty($order)) {
                return $this->sendJson(false, "Order not found");
            }
            DB::beginTransaction();
            $order->update([
                'order_price' => $request->order_price
            ]);
            $order->ordersDetail()->delete();
            foreach ($request->products as $key => $product) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_name' => $product['product_name'],
                    'product_price' => $product['product_price'],
                    'product_quantity' => $product['product_quantity'],
                ]);
            }
            DB::commit();
            return $this->sendJson(true, "Order Updated Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info("Order Creation");
            Log::info($th->getMessage());
            return $this->sendJson(false, "Something went wrong");
        }
    }

    public function show($id)
    {
        try {
            $user_id = auth()->user()->id;
            $order = Order::where('user_id', $user_id)->where('id', $id)->first();
            if (empty($order)) {
                return $this->sendJson(false, "Order not found");
            }
            $order = new OrderResource($order);
            return $this->sendJson(true, "Order Detail", $order);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info("Order Detail");
            Log::info($th->getMessage());
            return $this->sendJson(false, "Something went wrong");
        }
    }

    public function destroy($id)
    {
        try {
            $user_id = auth()->user()->id;
            $order = Order::where('user_id', $user_id)->where('id', $id)->first();
            if (empty($order)) {
                return $this->sendJson(false, "Order not found");
            }
            DB::beginTransaction();
            $order->ordersDetail()->delete();
            $order->delete();
            DB::commit();
            return $this->sendJson(true, "Order Deleted Successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info("Order Delete");
            Log::info($th->getMessage());
            return $this->sendJson(false, "Something went wrong");
        }
    }
}
