@extends('layouts.app')
@section('content')
<main class                         ="pt-90">
    <div class                      ="mb-4 pb-4"></div>
    <section class                  ="my-account container">
      <h2 class                     ="page-title">My Account</h2>
      <div class                    ="row">
        <div class                  ="col-lg-3">
         @include('user.account-nav')
        </div>
        <div class                  ="col-lg-9">
          <div class                ="page-content my-account__dashboard">
            <p>Hello <strong>{{ Auth::user()->name }}</strong></p>
            <p>"Welcome To Your E-cart world, shopper! Your cart is ready—let’s turn your wishlist into reality! </p>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection