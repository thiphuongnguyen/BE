<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Shipping;
use App\Models\Color;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $orders = Order::with(['customer' => function ($query) {
            $query->select('customer_id', 'customer_fullname');
        }])
        ->paginate($perPage);

        return response()->json($orders);
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
                'color_id' => $detail['color_id'],
                'product_image' => $detail['product_image'],
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

        // Lấy trạng thái trước đó của đơn hàng
        $previousStatus = $order->order_status;

        // Cập nhật trạng thái của đơn hàng
        $order->update([
            'order_status' => $request->input('order_status'),
        ]);

        // Nếu order_status là 1 hoặc 5, và trạng thái trước đó không phải là 1 hoặc 5, thực hiện cộng hoặc trừ product_sales_quantity từ bảng order_detail vào quantity từ bảng product_color
        if (in_array($request->input('order_status'), [1, 5]) && !in_array($previousStatus, [1, 5])) {
            $orderDetails = OrderDetail::where('order_id', $order_id)->get();

            foreach ($orderDetails as $orderDetail) {
                $productColor = ProductColor::where('product_id', $orderDetail->product_id)->first();

                if ($productColor) {
                    $updatedQuantity = $productColor->quantity + $orderDetail->product_sales_quantity;

                    $productColor->update([
                        'quantity' => $updatedQuantity >= 0 ? $updatedQuantity : 0,
                    ]);
                }
            }
        } elseif (in_array($request->input('order_status'), [2, 3, 4]) && !in_array($previousStatus, [2, 3, 4])) {
            // Nếu order_status là 2, 3 hoặc 4, và trạng thái trước đó không phải là 2, 3 hoặc 4, thực hiện trừ product_sales_quantity từ bảng order_detail khỏi quantity từ bảng product_color
            $orderDetails = OrderDetail::where('order_id', $order_id)->get();

            foreach ($orderDetails as $orderDetail) {
                $productColor = ProductColor::where('product_id', $orderDetail->product_id)->first();

                if ($productColor) {
                    $updatedQuantity = $productColor->quantity - $orderDetail->product_sales_quantity;
                    $productColor->update([
                        'quantity' => $updatedQuantity >= 0 ? $updatedQuantity : 0,
                    ]);
                }
            }
        }

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
        $colors = Color::get();

        $responseData = [
            'data' => $orders,
            'colors' => $colors,
        ];

        // Kiểm tra nếu không có đơn hàng nào được tìm thấy
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Không có đơn hàng cho customer_id này.'], 404);
        }

        // Trả về danh sách đơn hàng
        return response()->json( $responseData, 200);
    }

    public function getOrderDetail($orderId)
    {
        // Tìm đơn hàng theo ID với các thông tin liên quan như chi tiết đơn hàng và thông tin vận chuyển
        // $order = Order::with('orderDetail')->with('shipping')->with('customer')->findOrFail($orderId);
        $order = Order::with('orderDetail')->with('shipping')
        -> with(['customer' => function ($query) {
            $query->select('customer_id', 'customer_fullname', 'customer_image', 'customer_name', 'customer_phone');
        }])->findOrFail($orderId);
        $colors = Color::get();

        $responseData = [
            'data' => $order,
            'colors' => $colors,
        ];

        // Kiểm tra xem có tồn tại đơn hàng không
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Trả về đơn hàng và các chi tiết của đơn hàng đó, cùng với thông tin khách hàng
        return response()->json($responseData);
    }
    public function getDailySalesBetweenDates($start_date, $end_date)
    {
        // Truy vấn SQL để tính tổng tiền cho mỗi ngày trong khoảng ngày
        $daily_sales = DB::table('order')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(order_total) as total_sales'))
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        return $daily_sales;
    }

    public function countDistinctPayments()
    {
        // Gọi phương thức từ model để đếm số lượng các payment_id giống nhau
        $counts = Order::groupBy('payment_id')
            ->select('payment_id', Order::raw('count(*) as total'))
            ->pluck('total', 'payment_id');

        return $counts;
    }

}
