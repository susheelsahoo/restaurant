<div class="modal fade" id="kt_modal_add_gallery" tabindex="-1" aria-hidden="true" wire:ignore.self>

    <div class="modal-dialog modal-dialog-centered mw-650px">

        <div class="modal-content">

            <div class="modal-header" id="kt_modal_add_gallery_header">

                <h2 class="fw-bold">Add Gallery</h2>


                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross','fs-1') !!}
                </div>

            </div>


            <div class="modal-body px-5 my-7">

                <form id="kt_modal_add_gallery_form" class="form" action="#" wire:submit.prevent="submit" enctype="multipart/form-data">
                    <input type="hidden" wire:model.live="gallery_id" name="gallery_id" value="{{ $gallery_id }}" />

                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_gallery_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_gallery_header" data-kt-scroll-wrappers="#kt_modal_add_gallery_scroll" data-kt-scroll-offset="300px">

                        <div class="fv-row mb-7">

                            <label class="d-block fw-semibold fs-6 mb-5">Gallery Image</label>


                            <div class="image-input image-input-outline image-input-placeholder {{ $avatar || $saved_avatar ? '' : 'image-input-empty' }}" data-kt-image-input="true">

                                @if($avatar)
                                <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $avatar ? $avatar->temporaryUrl() : '' }});"></div>
                                @else
                                <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $saved_avatar }});"></div>
                                @endif


                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                    {!! getIcon('pencil','fs-7') !!}

                                    <input type="file" wire:model.live="avatar" name="avatar" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />

                                </label>


                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                    {!! getIcon('cross','fs-2') !!}
                                </span>


                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove image">
                                    {!! getIcon('cross','fs-2') !!}
                                </span>

                            </div>


                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>

                            @error('avatar')
                            <span class="text-danger">{{ $message }}</span> @enderror
                        </div>


                        <div class="fv-row mb-7">

                            <label class="required fw-semibold fs-6 mb-2">Gallery Name</label>


                            <input type="text" wire:model.live="name" name="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Gallery name" />

                            @error('name')
                            <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>


                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal" aria-label="Close" wire:loading.attr="disabled">Discard</button>
                        <button type="submit" class="btn btn-primary" data-kt-gallerys-modal-action="submit">
                            <span class="indicator-label" wire:loading.remove>Submit</span>
                            <span class="indicator-progress" wire:loading wire:target="submit">
                                Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>