<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $canceledOrders = Order::where('status', 'canceled')->count();
        return view('dashboard', compact('totalOrders', 'completedOrders', 'pendingOrders', 'canceledOrders'));

    }
}
