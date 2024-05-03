<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnpayPaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        // Lấy dữ liệu từ request
        $total = $request->input('total');
        $fee = $request->input('fee');
        $orderID= $request->input('order_id');

        // Các thông tin khác để tạo yêu cầu thanh toán
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = 'http://localhost:5555/thanks?orderID=' .$orderID ;
        $vnp_TmnCode = "UCBZ2V5W";
        $vnp_HashSecret = "CWINRJBCINWPAWOEALXHKCRYLMFGKENJ";
        $vnp_TxnRef = rand(00,9999);
        $vnp_OrderInfo = 'Thanh toán đơn hàng';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = ($total + $fee) * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $request->ip();

        // Tạo mảng dữ liệu cho yêu cầu thanh toán
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );
        
        // Sắp xếp mảng theo key
        ksort($inputData);

        // Tạo chuỗi dữ liệu cần mã hóa
        $hashdata = http_build_query($inputData);

        // Tạo mã hash
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        // Thêm mã hash vào URL thanh toán
        $vnp_Url .= "?" . $hashdata . '&vnp_SecureHash=' . $vnpSecureHash;

        // Trả về URL thanh toán
        return response()->json([
            'status' => 'success',
            'message' => 'Payment request has been processed',
            'data' => $vnp_Url
        ]);
    }
}
