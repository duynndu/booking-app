<?php

namespace App\Http\Controllers;

use App\Mail\MyEmail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MoMoController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'checkin_date' => 'required|date|after:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'payment_method' => 'required|in:momo,paypal,cash',
            'redirect_url' => 'required',
        ]);
        $room = Room::find($request->room_id);
        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }
        $bookedRooms = Booking::whereBetween('checkin_date', [$request->checkin_date, $request->checkout_date])
            ->orWhereBetween('checkout_date', [$request->checkin_date, $request->checkout_date])
            ->pluck('room_id');
        if ($bookedRooms->contains($room->id)) {
            return response()->json(['error' => 'Room is already booked']);
        }

        $partnerCode = "MOMO";
        $accessKey = "F8BBA842ECF85";
        $secretKey = "K951B6PE1waDMi640xX08PD3vg6EkVlz";
        $requestId = $partnerCode . time();
        $orderId = $requestId;
        $orderInfo = "pay with MoMo";
        $redirectUrl = $request->redirect_url;
        $ipnUrl = "http://booking-app.test/api/momo/momo-ipn";
        $amount = $room->price * (Carbon::parse($request->checkout_date)->diffInDays(Carbon::parse($request->checkin_date)));
        $requestType = "captureWallet";
        $extraData = ""; // pass empty value if your merchant does not have stores

        $rawSignature = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $requestBody = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
            'lang' => 'en',
        ];

        $response = Http::post('https://test-payment.momo.vn/v2/gateway/api/create', $requestBody);

        if ($response->successful()) {
            Booking::create([
                'room_id' => $request->room_id,
                'user_id' => $request->user_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'checkin_date' => Carbon::parse($request->checkin_date)->format('Y-m-d H:i:s'),
                'checkout_date' => Carbon::parse($request->checkout_date)->format('Y-m-d H:i:s'),
                'total_price' => $amount,
                'payment_method' => $request->payment_method,
                'payment_transaction_id' => $response->json()['orderId'],
                'is_paid' => false,
            ]);
            return response()->json($response->json());
        } else {
            return response()->json($response->json(), 500);
        }
    }

    public function momoIpn(Request $request)
    {
        $request->validate([
            'orderId' => 'required'
        ]);

        $booking = Booking::where('payment_transaction_id', $request->orderId)->first();
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        Mail::to('duynnz1901@gmail.com')->send(new MyEmail($booking));

        $booking->update(['is_paid' => true]);
        return response()->json(['message' => 'Payment received successfully']);
    }
}
