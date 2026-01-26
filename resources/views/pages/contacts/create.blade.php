<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($contact) ? 'Edit' : 'Create' }} Contact</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($contact) ? route('admin.contacts.update', $contact->id) : route('admin.contacts.store') }}" class="form">
                @csrf
                @if(isset($contact)) @method('PUT') @endif

                <div class="card-body border-top p-9">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Name -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Name</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name', $contact->name ?? '') }}" required />
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                        <div class="col-lg-8 fv-row">
                            <input type="email" name="email" class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', $contact->email ?? '') }}" required />
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Subject</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="subject" class="form-control form-control-lg form-control-solid @error('subject') is-invalid @enderror" placeholder="Subject" value="{{ old('subject', $contact->subject ?? '') }}" />
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Message</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="message" class="form-control form-control-lg form-control-solid @error('message') is-invalid @enderror" rows="5" placeholder="Message">{{ old('message', $contact->message ?? '') }}</textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                    <button type="submit" class="btn btn-primary">{{ isset($contact) ? 'Update' : 'Create' }} Contact</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>