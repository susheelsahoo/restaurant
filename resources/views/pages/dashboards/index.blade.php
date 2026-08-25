<x-default-layout>
    @section('title')
    Dashboard
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- New Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=pending') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$pendingStatus->color] ?? '#5b0ea8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $new_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">New Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Confirmed Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=confirmed&select_date=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$confirmedStatus->color] ?? '#17a2b8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $confirmed_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Confirmed Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- InHouse Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=in-house&select_date=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$inHouseStatus->color] ?? '#17a2b8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $in_house_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">In-House Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Complete Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=complete&select_date=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$completeStatus->color] ?? '#17a2b8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $complete_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Complete Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Admin To-Do List --}}
        <div class="col-12 mb-5 mb-xl-10">
            <section class="todo-dashboard-card">
                <div class="todo-dashboard-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="todo-dashboard-icon"><i class="ki-duotone ki-arrow-right fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                        <div>
                            <h2 class="todo-dashboard-title mb-1">Today's Handover <span class="todo-count todo-count-warning">{{ $pendingTasks->count() }}</span></h2>
                            <p class="todo-dashboard-subtitle mb-0">{{ now()->format('d F') }} · Information for today's shift</p>
                        </div>
                    </div>
                    <button type="button" class="btn todo-add-button" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i> Add task
                    </button>
                </div>

                <div class="todo-columns">
                    <div class="todo-column">
                        <div class="todo-column-heading"><span class="todo-dot todo-dot-warning"></span> PENDING <span class="todo-count ms-auto">{{ $pendingTasks->count() }}</span></div>
                        @forelse ($pendingTasks as $task)
                            <div class="todo-item">
                                <form method="POST" action="{{ route('admin.tasks.status.update', $task) }}">
                                    @csrf @method('PATCH')
                                    <button class="todo-checkbox" type="submit" aria-label="Mark {{ $task->title }} as completed"></button>
                                </form>
                                <div class="todo-item-content">
                                    <div class="todo-item-title">{{ $task->title }}</div>
                                    <div class="todo-item-meta">{{ $task->due_at->format('H:i') }} <span>·</span> {{ $task->description ?: 'General' }}</div>
                                </div>
                                @if ($task->due_at->isPast())
                                    <span class="todo-urgent">URGENT</span>
                                @endif
                            </div>
                        @empty
                            <div class="todo-empty">No pending tasks</div>
                        @endforelse
                    </div>

                    <div class="todo-column todo-completed-column">
                        <div class="todo-column-heading"><span class="todo-dot todo-dot-success"></span> COMPLETED <span class="todo-count ms-auto">{{ $completedTasks->count() }}</span></div>
                        @forelse ($completedTasks as $task)
                            <div class="todo-item todo-item-completed">
                                <form method="POST" action="{{ route('admin.tasks.status.update', $task) }}">
                                    @csrf @method('PATCH')
                                    <button class="todo-checkbox todo-checkbox-checked" type="submit" aria-label="Move {{ $task->title }} to pending"><i class="ki-duotone ki-check fs-3 text-white"><span class="path1"></span><span class="path2"></span></i></button>
                                </form>
                                <div class="todo-item-content">
                                    <div class="todo-item-title">{{ $task->title }}</div>
                                    <div class="todo-item-meta">{{ $task->completed_at?->format('H:i') }} <span>·</span> Completed</div>
                                </div>
                            </div>
                        @empty
                            <div class="todo-empty">No completed tasks</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        {{-- Total Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#F1416C;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $total_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Total Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Declined Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=declined') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$declinedStatus->color] ?? '#ffc107' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $declined_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Declined Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>



        {{-- Total Contacts --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/contacts') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:rgb(18,78,3);">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $total_contact }}</span>
                            <span class="text-white opacity-75 fs-6">Total Contacts</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- New Contacts --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/contacts?filter=new') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#033265;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $new_contact }}</span>
                            <span class="text-white opacity-75 fs-6">New Contacts</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Read Contacts --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/contacts?filter=read') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#6c757d;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $read_contact }}</span>
                            <span class="text-white opacity-75 fs-6">Read Contacts</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content todo-modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h2 class="modal-title" id="addTaskModalLabel">Add task</h2>
                        <p class="text-muted mb-0">Create a handover task for the team.</p>
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-dismiss="modal" aria-label="Close"><i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i></button>
                </div>
                <form method="POST" action="{{ route('admin.tasks.store') }}">
                    @csrf
                    <div class="modal-body pt-6">
                        <div class="mb-5">
                            <label class="form-label required">Task</label>
                            <input type="text" class="form-control form-control-solid" name="title" placeholder="Enter task title" value="{{ old('title') }}" required>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Description</label>
                            <textarea class="form-control form-control-solid" name="description" rows="3" placeholder="Add details for the team">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label required">Need to complete</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="due_at" value="{{ old('due_at') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn todo-add-button">Save task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .todo-dashboard-card { background: #fff; border: 1px solid #edf0f3; border-radius: 16px; overflow: hidden; box-shadow: 0 3px 12px rgba(31, 35, 45, .04); }
        .todo-dashboard-header { display: flex; align-items: center; justify-content: space-between; padding: 28px 30px; }
        .todo-dashboard-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 11px; color: #6741e8; background: #f0edff; }
        .todo-dashboard-title { color: #18243d; font-size: 20px; font-weight: 700; }
        .todo-dashboard-subtitle, .todo-item-meta { color: #98a1b2; font-size: 13px; }
        .todo-count { display: inline-flex; align-items: center; justify-content: center; min-width: 25px; height: 25px; padding: 0 7px; border-radius: 20px; background: #f1f3f5; color: #7d8797; font-size: 12px; font-weight: 700; }
        .todo-count-warning { margin-left: 8px; background: #fff1e6; color: #e9944b; vertical-align: 2px; }
        .todo-add-button { background: #6842e8; color: #fff; border: 0; border-radius: 10px; padding: 12px 17px; font-weight: 600; }
        .todo-add-button:hover { background: #5531d1; color: #fff; }
        .todo-columns { display: grid; grid-template-columns: 1fr 1fr; border-top: 1px solid #edf0f3; }
        .todo-column-heading { display: flex; align-items: center; gap: 10px; min-height: 56px; padding: 0 28px; color: #4d5665; background: #f8fafb; font-size: 13px; font-weight: 700; letter-spacing: .02em; }
        .todo-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .todo-dot-warning { background: #f6a623; } .todo-dot-success { background: #2bb673; }
        .todo-item { display: flex; align-items: center; gap: 16px; min-height: 82px; padding: 16px 28px; border-bottom: 1px solid #f0f2f4; }
        .todo-item:last-child { border-bottom: 0; }
        .todo-checkbox { width: 27px; height: 27px; flex: 0 0 27px; border: 2px solid #d4d9df; border-radius: 8px; background: #fff; cursor: pointer; }
        .todo-checkbox-checked { display: flex; align-items: center; justify-content: center; border-color: #2bb673; background: #2bb673; }
        .todo-item-content { min-width: 0; flex: 1; } .todo-item-title { color: #27344a; font-size: 15px; font-weight: 600; } .todo-item-meta { margin-top: 5px; } .todo-item-meta span { margin: 0 6px; }
        .todo-item-completed .todo-item-title { color: #8d96a3; text-decoration: line-through; } .todo-urgent { padding: 5px 9px; border-radius: 9px; color: #e65e77; background: #fff0f2; font-size: 10px; font-weight: 700; }
        .todo-empty { padding: 28px; color: #a5adba; font-size: 13px; text-align: center; }
        .todo-modal-content { border: 0; border-radius: 16px; box-shadow: 0 20px 60px rgba(20, 30, 50, .18); }
        @media (max-width: 767px) { .todo-dashboard-header { align-items: flex-start; flex-direction: column; gap: 18px; } .todo-add-button { width: 100%; } .todo-columns { grid-template-columns: 1fr; } .todo-completed-column { border-top: 1px solid #edf0f3; } }
    </style>

</x-default-layout>
