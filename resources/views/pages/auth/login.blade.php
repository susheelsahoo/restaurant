<x-auth-layout>
    <style>
        .tifliso-login-header {
            text-align: center;
            margin-bottom: 2.75rem;
        }

        .tifliso-login-eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: #d4af37;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .tifliso-login-title {
            color: #fff9ee;
            font-size: clamp(2rem, 3vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 0.75rem;
        }

        .tifliso-login-subtitle {
            color: rgba(245, 241, 232, 0.66);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .tifliso-login-label {
            display: block;
            color: #f0e2b0;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.85rem;
        }

        .tifliso-login-input {
            min-height: 58px;
            border-radius: 18px;
            border: 1px solid rgba(212, 175, 55, 0.18);
            background: rgba(255, 255, 255, 0.03);
            color: #fff9ee;
            padding: 0.95rem 1.15rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .tifliso-login-input::placeholder {
            color: rgba(245, 241, 232, 0.38);
        }

        .tifliso-login-input:focus {
            border-color: rgba(212, 175, 55, 0.45);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.12);
        }

        .tifliso-login-link {
            color: #d4af37;
            text-decoration: none;
        }

        .tifliso-login-link:hover {
            color: #f2d57c;
        }

        .tifliso-login-btn {
            min-height: 58px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #d4af37 0%, #f2d57c 100%);
            color: #17120a;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            box-shadow: 0 14px 28px rgba(212, 175, 55, 0.18);
        }

        .tifliso-login-btn:hover,
        .tifliso-login-btn:focus {
            color: #17120a;
            background: linear-gradient(135deg, #e0bc4d 0%, #f7df96 100%);
        }

        .tifliso-login-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(245, 241, 232, 0.46);
            margin: 2rem 0;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .tifliso-login-divider::before,
        .tifliso-login-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.2), transparent);
        }

        .tifliso-login-bottom {
            color: rgba(245, 241, 232, 0.64);
            text-align: center;
            font-weight: 600;
        }
    </style>
    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('admin.dashboard') }}" action="{{ route('admin.login') }}">
        @csrf
        <div class="tifliso-login-header">
            <div class="tifliso-login-eyebrow">Restaurant Admin</div>
            <h1 class="tifliso-login-title">Sign in to continue</h1>
            <p class="tifliso-login-subtitle">
                Access bookings, customers, and promotional tools from one refined workspace.
            </p>
        </div>
        <div class="tifliso-login-divider">
            <span>Email Login</span>
        </div>
        <div class="fv-row mb-8">
            <label class="tifliso-login-label">Email Address</label>
            <input type="text" placeholder="Enter your email" name="email" autocomplete="off" class="form-control tifliso-login-input" />
        </div>
        <div class="fv-row mb-4">
            <label class="tifliso-login-label">Password</label>
            <input type="password" placeholder="Enter your password" name="password" autocomplete="off" class="form-control tifliso-login-input" />
        </div>
        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
            <div></div>
            <a href="{{ route('admin.password.request') }}" class="tifliso-login-link">
                Forgot Password?
            </a>
        </div>
        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_in_submit" class="btn tifliso-login-btn">
                @include('partials/general/_button-indicator', ['label' => 'Sign In'])
            </button>
        </div>
        <!-- <div class="tifliso-login-bottom fs-6">
            Not a Member yet?
            <a href="{{ route('admin.register') }}" class="tifliso-login-link">
                Sign up
            </a>
        </div>-->
    </form>
</x-auth-layout>