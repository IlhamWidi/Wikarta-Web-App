@extends('layouts.main')
@section('style')
@endsection

@section('content')
    <!-- TABLE START -->
    <div class="row">
        <div class="col-xl-12" id="card-none2">

            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-table"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Data User Admin</span>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('user-admin.create') }}" class="btn btn-success btn-sm">
                                <i class="bx bx-plus"></i> Create
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body collapse show" id="collapseexample2">

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('user-admin') }}" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-0">Name</label>
                                <input type="text" name="name" value="{{ request('name') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-0">Username</label>
                                <input type="text" name="username" value="{{ request('username') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-0">Roles</label>
                                <select name="role_id" class="form-control form-control-sm">
                                    <option value="">All Roles</option>
                                    @foreach($ms_roles as $role)
                                        <option value="{{ $role }}" {{ request('role_id') == $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bx bx-search"></i> Filter
                                </button>
                                <a href="{{ route('user-admin') }}" class="btn btn-light btn-sm">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                    <!-- End Filter Form -->

                    <!-- Desktop Table -->
                    <div class="d-none d-md-block">
                        <table class="table table-bordered dt-responsive nowrap table-striped align-middle"
                            style="width:100%;font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data))
                                    @foreach($data as $k => $v)
                                        <tr>
                                            <td style="text-align: center;">
                                                {{ ($data->currentPage() - 1) * $data->perPage() + $k + 1 }}
                                            </td>
                                            <td style="color: red;">{{ $v->code }}</td>
                                            <td>{{ $v->name ?? '-' }}</td>
                                            <td><a href="">{{ $v->username }}</a></td>
                                            <td>{{ $v->phone_number }}</td>
                                            <td>{{ $v->email }}</td>
                                            <td>{{ $v->role_id }}</td>
                                            <td style="text-align: center;">
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        style="border: 1px solid #cfcfcf;">
                                                        <i class="ri-more-fill align-middle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item edit-item-btn"
                                                                href="{{ route('user-admin.edit', ['id' => $v->id]) }}">
                                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete"
                                                                data-id="{{ $v['id'] }}"
                                                                class="dropdown-item remove-item-btn delete-form">
                                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div>
                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                    <!-- Mobile List View -->
                    <div class="d-md-none">
                        @if(isset($data))
                            @foreach($data as $v)
                                <div class="card mb-2 border">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold" style="color: red;">{{ $v->code }}</span>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="border: 1px solid #cfcfcf;">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item edit-item-btn"
                                                            href="{{ route('user-admin.edit', ['id' => $v->id]) }}">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete"
                                                            data-id="{{ $v['id'] }}"
                                                            class="dropdown-item remove-item-btn delete-form">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div>
                                            <div><b>Name:</b> {{ $v->name ?? '-' }}</div>
                                            <div><b>Username:</b> {{ $v->username }}</div>
                                            <div><b>Email:</b> {{ $v->email }}</div>
                                            <div><b>Roles:</b> {{ $v->role_id }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div>
                                {{ $data->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- TABLE END -->
    @include('pages.user.admin.modal')
@endsection

@section('script')
    <script>
        $(document).on('click', '.delete-form', function () {
            let id = $(this).attr('data-id');
            $('#id_delete').val(id);
        });
    </script>
@endsection