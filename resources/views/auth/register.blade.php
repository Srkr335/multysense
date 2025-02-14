<head>
    <title>Register</title>
    <link rel                       = "stylesheet" href            = "{{ asset('css/login.css') }}">
</head>

<main class                         = "pt-90">
    <div class                      = "mb-4 pb-4"></div>
    <section class                  = "login-register container">
        <h2 class                   = "form-title">Create Your Account</h2>
        <div class                  = "register-form">
            <form method            = "POST" action                = "{{ route('register') }}" class           = "needs-validation" novalidate>
                @csrf


                <div class          = "form-floating mb-3">
                    <input type     = "text" class                 = "form-control @error('name') is-invalid @enderror" 
                           name     = "name" value                 = "{{ old('name') }}" required autocomplete = "name" placeholder                   = "Full Name" autofocus>
                    @error('name')
                        <span class = "invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>


                <div class          = "form-floating mb-3">
                    <input type     = "email" class                = "form-control @error('email') is-invalid @enderror" 
                           name     = "email" value                = "{{ old('email') }}" placeholder          = "Email " required autocomplete       = "email">
                    @error('email')
                        <span class = "invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>


                <div class          = "form-floating mb-3">
                    <input type     = "text" class                 = "form-control @error('mobile') is-invalid @enderror" 
                           name     = "mobile" value               = "{{ old('mobile') }}" placeholder         = "Mobile Number"required autocomplete = "mobile">
                    @error('mobile')
                        <span class = "invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>


                <div class          = "form-floating mb-3">
                    <input type     = "password" class             = "form-control @error('password') is-invalid @enderror" 
                           name     = "password" placeholder       = "Password" required autocomplete          = "new-password">
                    @error('password')
                        <span class = "invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>


                <div class          = "form-floating mb-3">
                    <input type     = "password" class             = "form-control"  placeholder               = "Confirm Password"   name            = "password_confirmation" required autocomplete = "new-password">
                </div>


                <button class       = "btn btn-primary w-100" type = "submit">Register</button>



                <div class          = "customer-option mt-4 text-center">
                    <span class     = "text-secondary">Already have an account?</span>
                    <a href         = "{{ route('login') }}" class = "btn-text">Login Here</a>
                </div>
            </form>
        </div>
    </section>
</main>
