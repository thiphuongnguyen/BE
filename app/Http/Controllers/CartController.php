<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($customer_id)
    {
        
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
        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'products' => 'required|array',
        ]);
    
        $customerId = $request->input('customer_id');
        $products = $request->input('products');
    
        foreach ($products as $product) {
            $productId = $product['product_id'];
            $colorId = $product['color_id'];
            $quantity = $product['product_quantity'];
    
            // Nếu sản phẩm đã tồn tại trong giỏ hàng của khách hàng, cập nhật quantity
            Cart::updateOrCreate(
                // ['customer_id' => $customerId, 'product_id' => $productId],
                ['customer_id' => $customerId, 'product_id' => $productId, 'color_id' => $colorId],
                ['product_quantity' => \DB::raw("product_quantity + $quantity")]
            );
        }
    
        return response()->json(['message' => 'Cart updated successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $customer_id)
    {
        // Validate dữ liệu yêu cầu
        $request->validate([
            'product_update' => 'required|array',
        ]);

        // Chuyển đổi dữ liệu JSON thành mảng
        $productUpdates = $request->input('product_update');

        foreach ($productUpdates as $productUpdate) {
            // Validate dữ liệu của mỗi sản phẩm
            $validator = Validator::make($productUpdate, [
                'product_id' => 'required|numeric',
                'color_id' => 'required|numeric',
                'product_quantity' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Dữ liệu sản phẩm không hợp lệ', 'errors' => $validator->errors()], 400);
            }

            // Tìm kiếm mục giỏ hàng liên quan đến khách hàng và sản phẩm
            $cart = Cart::where('customer_id', $customer_id)
                        ->where('product_id', $productUpdate['product_id'])
                        ->where('color_id', $productUpdate['color_id'])
                        ->first();

            if ($cart) {
                // Cập nhật mục giỏ hàng với số lượng sản phẩm mới
                $cart->update([
                    'product_quantity' => $productUpdate['product_quantity'],
                ]);
            } else {
                // Tùy chọn: Tạo một mục giỏ hàng mới nếu sản phẩm chưa tồn tại trong giỏ hàng
                Cart::create([
                    'customer_id' => $customer_id,
                    'product_id' => $productUpdate['product_id'],
                    'color_id' => $productUpdate['color_id'],
                    'product_quantity' => $productUpdate['product_quantity'],
                ]);
            }
        }

        // Trả về thông báo thành công
        return response()->json(['message' => 'Cập nhật mục giỏ hàng thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }

    public function getCartProducts($customerId)
    {
        $cartItems = Cart::with([
            'productDetail' => function ($query) {
                $query->select('product_id', 'product_name', 'product_image', 'product_sale');
            },
            'productColors' => function ($query) {
                $query->select('product_id', 'color_id', 'quantity', 'product_price');
            },
        ])->where('customer_id', $customerId)->get(['customer_id', 'product_id', 'color_id', 'product_quantity']);

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty', 'data' => []]);
        }
        $colors = Color::get();

        $responseData = [
            'data' => $cartItems,
            'colors' => $colors,
        ];

        return response()->json($responseData);
    }


    public function deleteProductFromCart($customerId, $productId)
    {
        $cartItem = Cart::where('customer_id', $customerId)
                        ->where('product_id', $productId)
                        ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in the cart'], 404);
        }

        // Delete the cart item
        $cartItem->delete();

        return response()->json(['message' => 'Product deleted from the cart']);
    }
    public function deleteAllProductsFromCart($customerId)
    {
        $cartItems = Cart::where('customer_id', $customerId)->get();
    
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is already empty']);
        }
    
        $cartItems->each(function ($cartItem) {
            $cartItem->delete();
        });
    
        return response()->json(['message' => 'All products deleted from the cart']);
    }
}
