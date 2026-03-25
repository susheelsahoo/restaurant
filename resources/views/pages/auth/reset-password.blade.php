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

        .tifliso-login-secondary-btn {
            min-height: 58px;
            border-radius: 999px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            background: transparent;
            color: #f5f1e8;
            font-weight: 700;
        }

        .tifliso-login-secondary-btn:hover {
            color: #fff9ee;
            border-color: rgba(212, 175, 55, 0.4);
            background: rgba(255, 255, 255, 0.04);
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

        .tifliso-password-meter [data-kt-password-meter-control="highlight"] > div {
            background: rgba(255, 255, 255, 0.08) !important;
        }

        .tifliso-password-meter .bg-active-success.active {
            background: linear-gradient(90deg, #d4af37, #f2d57c) !important;
        }

        .tifliso-password-hint,
        .tifliso-checkbox-label {
            color: rgba(245, 241, 232, 0.66);
        }

        .tifliso-checkbox-label a {
            color: #d4af37;
            text-decoration: none;
        }

        .tifliso-password-toggle {
            color: rgba(245, 241, 232, 0.62);
        }
    </style>

    <!--begin::Form-->
    <form class="form w-100" novalidate="novalidate" id="kt_new_password_form" data-kt-redirect-url="{{ route('admin.login') }}" action="{{ route('admin.password.update') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->token }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        <!--begin::Heading-->
        <div class="tifliso-login-header">
            <div class="tifliso-login-eyebrow">Password Recovery</div>
            <h1 class="tifliso-login-title">Create New Password</h1>
            <div class="tifliso-login-subtitle">
                Set a secure new password to restore access to your admin account.
            </div>
        </div>
        <!--begin::Heading-->

        <div class="tifliso-login-divider">
            <span>Update Password</span>
        </div>

        <!--begin::Input group-->
        <div class="fv-row mb-8 tifliso-password-meter" data-kt-password-meter="true">
            <label class="tifliso-login-label">New Password</label>
            <!--begin::Wrapper-->
            <div class="mb-1">
                <!--begin::Input wrapper-->
                <div class="position-relative mb-3">
                    <input class="form-control tifliso-login-input" type="password" placeholder="Enter new password" name="password" autocomplete="off" />

                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 tifliso-password-toggle" data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
                <!--end::Input wrapper-->

                <!--begin::Meter-->
                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                </div>
                <!--end::Meter-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Hint-->
            <div class="tifliso-password-hint">
                Use 8 or more characters with a mix of letters, numbers & symbols.
            </div>
            <!--end::Hint-->
        </div>
        <!--end::Input group--->

        <!--end::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Repeat Password-->
            <label class="tifliso-login-label">Confirm Password</label>
            <input placeholder="Repeat password" name="password_confirmation" type="password" autocomplete="off" class="form-control tifliso-login-input" />
            <!--end::Repeat Password-->
        </div>
        <!--end::Input group--->

        <!--begin::Input group--->
        <div class="fv-row mb-10">
            <div class="form-check form-check-custom form-check-solid form-check-inline">
                <input class="form-check-input" type="checkbox" name="toc" value="1" />

                <label class="form-check-label fw-semibold fs-6 tifliso-checkbox-label">
                    I Agree &

                    <a href="#" class="ms-1">Terms and conditions</a>.
                </label>
            </div>
        </div>
        <!--end::Input group--->

        <!--begin::Actions-->
        <div class="d-flex flex-column flex-sm-row gap-4 justify-content-center pb-lg-0">
            <button type="button" id="kt_new_password_submit" class="btn tifliso-login-btn me-sm-0">
                @include('partials/general/_button-indicator', ['label' => 'Submit'])
            </button>

            <a href="{{ route('admin.login') }}" class="btn tifliso-login-secondary-btn">Back to Login</a>
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Form-->

</x-auth-layout>
