<?php

namespace App\Livewire\Gallery;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\GalleryImage;
use Illuminate\Support\Facades\Storage;

class AddGalleryModal extends Component
{
    use WithFileUploads;

    public $gallery_id;
    public $name;
    public $avatar;
    public $saved_avatar;
    public $edit_mode = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'avatar' => 'nullable|image|max:2048',
    ];

    protected $listeners = [
        'editGallery' => 'editGallery',
        'newGallery' => 'resetForm',
        'closeModal' => 'closeModal',
        'deleteGallery' => 'deleteGallery',
    ];

    public function render()
    {
        return view('livewire.gallery.add-gallery-modal');
    }

    public function submit()
    {
        $this->validate([
            'avatar' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'name' => 'required|string|max:255',
        ]);

        $imagePath = $this->avatar->store('gallery_images', 'public');

        GalleryImage::create([
            'title' => $this->name,
            'image_path' => $imagePath,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // dispatch event to close modal and redirect
        $this->dispatch('closeModal');
        session()->flash('message', 'Gallery image added successfully!');
        return redirect()->route('admin.gallery.index');
    }

    public function editGallery($id)
    {
        $this->edit_mode = true;
        $gallery = GalleryImage::findOrFail($id);

        $this->gallery_id = $gallery->id;
        $this->name = $gallery->name;
        $this->saved_avatar = $gallery->image ? asset('storage/' . $gallery->image) : null;
    }

    public function deleteGallery($id)
    {
        $galleryImage = GalleryImage::find($id);
        if ($galleryImage) {
            $galleryImage->delete();
            $this->emit('success', 'Image deleted successfully!');
        } else {
            $this->emit('success', 'Image not found!');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'gallery_id',
            'name',
            'avatar',
            'saved_avatar',
            'edit_mode',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
    }
}
