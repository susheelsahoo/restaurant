<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\GalleryImage;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class GalleryImageDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['image', 'is_active', 'action', 'copy_icon']) // Add 'copy_icon' to raw columns
            ->editColumn('image', function (GalleryImage $galleryImage) {
                return '<img src="' . asset('storage/' . $galleryImage->image_path) . '" width="100" height="100">';
            })
            ->editColumn('is_active', function (GalleryImage $galleryImage) {
                return $galleryImage->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })
            ->editColumn('created_at', function (GalleryImage $galleryImage) {
                return \Carbon\Carbon::parse($galleryImage->created_at)->format('d M Y, H:i:s');
            })
            ->addColumn('action', function (GalleryImage $galleryImage) {
                return view('pages/gallery.columns._actions', compact('galleryImage'));
            })
            ->addColumn('copy_icon', function (GalleryImage $galleryImage) {
                return '<button class="badge badge-info copy-btn" data-clipboard-text="' . asset('storage/' . $galleryImage->image_path) . '">
                        <i class="fas fa-copy"></i> Copy Path
                    </button>';
            })
            ->setRowId('id');
    }
    public function query(GalleryImage $model): QueryBuilder
    {
        return $model->newQuery();
    }
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('gallery-images-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12'tr>><'d-flex justify-content-between'<'col-sm-12 col-md-5'i><'d-flex justify-content-between'p>>",)
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(2)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/gallery/columns/_draw-scripts.js')) . "}");
    }
    public function getColumns(): array
    {
        return [
            Column::make('image')->title('Image')->addClass('d-flex align-items-center'),
            Column::make('title')->title('Title'),
            Column::make('is_active')->title('Status'),
            Column::computed('copy_icon')->title('Copy Path')->addClass('text-center'),  // Add copy icon column
            Column::make('created_at')->title('Created At')->addClass('text-nowrap'),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
    protected function filename(): string
    {
        return 'GalleryImages_' . date('YmdHis');
    }
}
