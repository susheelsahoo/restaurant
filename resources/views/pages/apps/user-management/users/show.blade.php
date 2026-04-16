<x-default-layout>

    @section('title')
    Users
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('user-management.users.show', $user) }}
    @endsection

    <!--begin::Layout-->
    <div class="d-flex justify-content-center">
        <!--begin::Card wrapper-->
        <div class="w-100 w-xl-700px mx-auto mb-10">
            <!--begin::Card-->
            <div class="card mb-5 mb-xl-8">
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="d-flex flex-center flex-column py-5">
                        <!--begin::Avatar-->
                        <div class="symbol symbol-100px symbol-circle mb-7">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="image" />
                            @else
                                <div class="symbol-label fs-3 {{ app(\App\Actions\GetThemeType::class)->handle('bg-light-? text-?', $user->name) }}">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <!--end::Avatar-->
                        <!--begin::Header-->
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between w-100 mb-4">
                            <div class="me-0 me-md-4 text-center text-md-start">
                                <div class="fs-3 text-gray-800 fw-bold">{{ $user->name }}</div>
                                <div class="text-muted">Account ID: #{{ $user->id }}</div>
                            </div>
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary mt-4 mt-md-0" wire:click="$dispatch('update_user', { id: {{ $user->id }} })" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">Edit details</a>
                        </div>
                        <!--end::Header-->
                        <!--begin::Roles-->
                        <div class="mb-9 text-center">
                            @forelse($user->roles as $role)
                                <div class="badge badge-lg badge-light-primary d-inline me-2 mb-2">ID {{ $role->id }} - {{ ucwords($role->name) }}</div>
                            @empty
                                <div class="text-muted">No roles assigned</div>
                            @endforelse
                        </div>
                        <!--end::Roles-->
                        <div class="fs-6 fw-semibold text-muted text-center">Only essential user details are shown on this page.</div>
                    </div>

                    <div class="separator"></div>

                    <div class="py-5 fs-6">
                        <div class="row gy-5">
                            <div class="col-md-6">
                                <div class="fw-bold">Email</div>
                                <div class="text-gray-600">
                                    <a href="mailto:{{ $user->email }}" class="text-gray-600 text-hover-primary">{{ $user->email }}</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Email status</div>
                                <div class="text-gray-600">{{ $user->email_verified_at ? 'Verified' : 'Not verified' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Registered</div>
                                <div class="text-gray-600">{{ optional($user->created_at)->format('d M Y, H:i') ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold">Last Login</div>
                                <div class="text-gray-600">{{ optional($user->last_login_at)->format('d M Y, H:i') ?? 'Never' }}</div>
                            </div>
                            @if($user->last_login_ip)
                                <div class="col-md-6">
                                    <div class="fw-bold">Last Login IP</div>
                                    <div class="text-gray-600">{{ $user->last_login_ip }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Permissions</h3>
                    </div>
                </div>
                <div class="card-body pt-2">
                    @php
                        $permissions = $user->getAllPermissions()->sortBy('name');
                    @endphp

                    @if($permissions->isEmpty())
                        <div class="text-muted">No permissions assigned.</div>
                    @else
                        <div class="row g-3">
                            @foreach($permissions as $permission)
                                <div class="col-6">
                                    <div class="badge badge-light-primary">{{ ucwords(str_replace(['-', '_'], ' ', $permission->name)) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <!--end::Permissions-->
        </div>
        <!--end::Card wrapper-->
    </div>
    <!--end::Layout-->

    <!--begin::Modal-->
    <livewire:user.add-user-modal></livewire:user.add-user-modal>
    <!--end::Modal-->

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', function () {
                Livewire.on('success', function () {
                    $('#kt_modal_add_user').modal('hide');
                    // Reload page to see updated user details
                    setTimeout(() => window.location.reload(), 500);
                });
            });
        </script>
    @endpush

</x-default-layout>
