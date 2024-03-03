<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipping;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = 16;
        $orders = Order::with('orderDetail')->with('shipping')->paginate($perPage);
        $responseData = [
            'data' => $orders,
        ];

        return response()->json($responseData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'customer_id' => 'required|exists:customers,customer_id',
            'shipping_id' => 'numeric',
            'payment_id' => 'numeric',
            'order_total' => 'numeric',
            'order_status' => 'numeric',
        ];

        $request->validate($rules);

        // add data in Shipping table
        $shippingInfo = $request->input('shipping_info');
        $shipping = Shipping::create([
            'shipping_name' => $shippingInfo['shipping_name'],
            'shipping_address' => $shippingInfo['shipping_address'],
            'shipping_phone' => $shippingInfo['shipping_phone'],
            'shipping_notes' => $shippingInfo['shipping_notes'],
        ]);

        $shipping_id = $shipping->shipping_id;

        // Create the product if validation passes
        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'shipping_id' => $shipping_id,
            'payment_id' => $request->input('payment_id'),
            'order_total' => $request->input('order_total'),
            'order_status' => $request->input('order_status'),
            'created_at'=> now(),
        ]);

        $order_id = $order->order_id;

        $orderDetails = $request->input('order_detail');
        foreach ($orderDetails as $detail) {
            OrderDetail::create([
                'order_id' => $order_id,
                'product_id' => $detail['product_id'],
                'product_name' => $detail['product_name'],
                'product_price' => $detail['product_price'],
                'product_sales_quantity' => $detail['product_sales_quantity'],
            ]);
        }

        return response()->json(['message' => 'Order created successfully', 'data' => $order]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        // Kiểm tra xem đơn hàng tồn tại không
        $order = Order::find($order_id);

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'order_status' => 'required',
        ]);

        // Cập nhật trạng thái của đơn hàng
        $order->update([
            'order_status' => $request->input('order_status'),
        ]);

        // Trả về thông báo thành công
        return response()->json(['message' => 'Cập nhật trạng thái đơn hàng thành công.'], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function getOrdersByCustomerId($customer_id)
    {
        // Lấy ra các đơn hàng của một customer_id cụ thể
        $orders = Order::where('customer_id', $customer_id)->with('orderDetail')->with('shipping')->get();

        // Kiểm tra nếu không có đơn hàng nào được tìm thấy
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Không có đơn hàng cho customer_id này.'], 404);
        }

        // Trả về danh sách đơn hàng
        return response()->json(['orders' => $orders], 200);
    }
}
