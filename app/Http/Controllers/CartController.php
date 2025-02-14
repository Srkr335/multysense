<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::instance('cart')->content();
        return view('cart',compact('cartItems'));
    }  

    public function addToCart(Request $request)
{
    Cart::instance('cart')->add(
        $request->id,
        $request->name,
        $request->quantity,
        $request->price
    )->associate('App\Models\Product');
    session()->flash('success', 'Product is Added to Cart Successfully !');

    if ($request->ajax()) {
        return response()->json(['status' => 200, 'message' => 'Success! Item successfully added to your cart.']);
    }

    return redirect()->route('cart.index');
}


    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }
    
    public function reduce_cart_quantity($rowId){
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }
    
    public function remove_item($rowId)
{
    Cart::instance('cart')->remove($rowId);
    return redirect()->back();
}


public function empty_cart()
{
    Cart::instance('cart')->destroy();
    return redirect()->back();
}


public function checkout()
{
    if(!Auth::check())
    {
        return redirect()->route("login");
    }
    $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
    return view('checkout',compact("address"));
}
public function place_an_order(Request $request)
{
    $user_id = Auth::id();

    $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
    if (!$address) {
        $request->validate([
            'name' => 'required|max:100',
            'phone' => 'required|numeric|digits:10',
            'zip' => 'required|numeric|digits:6',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required'
        ]);

        $address = Address::create([
            'user_id' => $user_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'zip' => $request->zip,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'locality' => $request->locality,
            'landmark' => $request->landmark,
            'country' => '',
            'isdefault' => true
        ]);
    }

    $this->setAmountForCheckout();
    
    $checkout = session()->get('checkout', []);
    if (empty($checkout)) {
        return redirect()->route('cart.index')->with('error', 'Checkout session expired. Please try again.');
    }

    $order = Order::create([
        'user_id' => $user_id,
        'subtotal' => floatval(str_replace(',', '', $checkout['subtotal'] ?? 0)),
        'discount' => floatval(str_replace(',', '', $checkout['discount'] ?? 0)),
        'tax' => floatval(str_replace(',', '', $checkout['tax'] ?? 0)),
        'total' => floatval(str_replace(',', '', $checkout['total'] ?? 0)),
        'name' => $address->name,
        'phone' => $address->phone,
        'locality' => $address->locality,
        'address' => $address->address,
        'city' => $address->city,
        'state' => $address->state,
        'country' => $address->country,
        'landmark' => $address->landmark,
        'zip' => $address->zip
    ]);

    foreach (Cart::instance('cart')->content() as $item) {
        if (empty($item->id)) {
         \Illuminate\Support\Facades\Log::error("Missing product_id for cart item: " . json_encode($item));
         continue;
        }
    
        OrderItem::create([
            'product_id' => $item->id,
            'order_id'   => $order->id,
            'price'      => $item->price,
            'quantity'   => $item->qty
        ]);
    }
    
    Transaction::create([
        'user_id' => $user_id,
        'order_id' => $order->id,
        'mode' => $request->mode,
        'status' => "pending"
    ]);

    Cart::instance('cart')->destroy();
    session()->forget(['checkout', 'coupon', 'discounts']);
    session()->put('order_id', $order->id);
    
    return redirect()->route('cart.order_confirmation');
}


public function setAmountForCheckout()
{
    if (Cart::instance('cart')->count() <= 0) {
        session()->forget('checkout');
        return;
    }

    if (session()->has('discounts') && is_array(session()->get('discounts'))) {
        session()->put('checkout', [
            'discount' => session()->get('discounts')['discount'] ?? 0,
            'subtotal' => session()->get('discounts')['subtotal'] ?? Cart::instance('cart')->subtotal(),
            'tax' => session()->get('discounts')['tax'] ?? Cart::instance('cart')->tax(),
            'total' => session()->get('discounts')['total'] ?? Cart::instance('cart')->total()
        ]);
    } else {
        session()->put('checkout', [
            'discount' => 0,
            'subtotal' => Cart::instance('cart')->subtotal(),
            'tax' => Cart::instance('cart')->tax(),
            'total' => Cart::instance('cart')->total()
        ]);
    }
}

public function order_confirmation()
{
if(Session::has('order_id')){
    $order = Order::find(Session::get('order_id'));
    return view('order_confirmation',compact('order'));
}
    return redirect()->route('cart.index');
}

}
