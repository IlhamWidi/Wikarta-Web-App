@extends('layouts.main')
@section('style')
@endsection

@section('content')
    <form method="post"
        action="{{ isset($one) ? route('user-admin.update', ['id' => $one->id]) : route('user-admin.store') }}">@csrf
        <div class="row">
            <div class="col-xl-12" id="card-none1">
                <div class="card">
                    <div class="card-header pt-2 pb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <li class="bx bx-edit"></li>&nbsp;&nbsp;
                                <span class="card-title" style="font-size: 15px;"> Form User Admin</span>
                            </div>
                            <div class="flex-shrink-0">
                            </div>
                        </div>
                    </div>
                    <div class="card-body collapse show" id="collapseexample1">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="basiInput" class="form-label mb-0">ID</label>
                                <input type="text" class="form-control" disabled value="{{ $one->code ?? 'GENERATED' }}">
                                <label for="basiInput" class="form-label mb-0">Name</label>
                                <input name="name" type="text" class="form-control" value="{{ $one->name ?? old('name') }}">
                                <label for="basiInput" class="form-label mb-0">Username</label>
                                <input name="username" type="text" class="form-control"
                                    value="{{ $one->username ?? old('username') }}">
                                <label for="basiInput" class="form-label mb-0">Phone Number</label>
                                <input name="phone_number" type="text" class="form-control" value="{{ $one->phone_number ?? old('phone_number') }}">
                            </div>
                            <div class="col-lg-4">
                                <label for="choices-single-default" class="form-label mb-0">Roles</label>
                                <select class="form-control js-example-basic-single" name="role_id">
                                    <option value="">Select Roles</option>
                                    @if(isset($ms_roles))
                                        @foreach($ms_roles as $x)
                                            <option value="{{ $x }}" <?= isset($one->role_id) && $one->role_id == $x ? 'selected=selected' : ''; ?>>{{ $x }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="iconrightInput" class="form-label mb-0">Email</label>
                                <div class="form-icon right">
                                    <input name="email" type="email" class="form-control form-control-icon"
                                        id="iconrightInput" placeholder="example@email.com"
                                        value="{{ $one->email ?? old('email') }}">
                                    <i class="ri-mail-unread-line"></i>
                                </div>
                                <label for="iconrightInput" class="form-label mb-0">Password</label>
                                <div class="form-icon right">
                                    <input name="password" type="password" class="form-control form-control-icon"
                                        id="iconrightInput" placeholder="">
                                    <i class="ri-phone-line"></i>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="choices-single-default" class="form-label mb-0">City / Province</label>
                                <select class="form-control js-example-basic-single" name="city_id">
                                    <option value="">Select City / Province</option>
                                    @if(isset($ms_cities))
                                        @foreach($ms_cities as $x)
                                            <option value="{{ $x }}" <?= isset($one->city_id) && $one->city_id == $x ? 'selected=selected' : ''; ?>>{{ $x }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="exampleFormControlTextarea5" class="form-label mb-0">Address</label>
                                <textarea name="address" style="max-height: 95px;" class="form-control"
                                    id="exampleFormControlTextarea5" rows="4">{{ $one->address ?? '-' }}</textarea>
                                <label class="form-label">Cabang yang Diizinkan</label>
                                <select class="form-select" id="choices-multiple-remove-button" data-choices
                                    data-choices-removeItem name="allowed_branches[]" multiple>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ isset($one) && is_array($one->allowed_branches) && in_array($branch->id, $one->allowed_branches) ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Pilih beberapa cabang yang diizinkan untuk user ini</div>

                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="mb-2">Management Menu</h5>
                                <div class="card p-3 mb-3" style="background: #f9f9f9;">
                                    @foreach($menus->where('parent_id', null) as $parent)
                                        <div class="mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="menu_parent_{{ $parent->id }}"
                                                    name="menu_access[{{ $parent->id }}][read]"
                                                    value="read"
                                                    {{ isset($menu_access[$parent->id]['read']) && $menu_access[$parent->id]['read'] ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="menu_parent_{{ $parent->id }}">
                                                    {{ $parent->name }}
                                                </label>
                                            </div>
                                            <div class="ms-4">
                                                @foreach($menus->where('parent_id', $parent->id) as $child)
                                                    <div class="row align-items-center mb-1">
                                                        <div class="col-md-4 col-12">
                                                            <label class="form-label mb-0">{{ $child->name }}</label>
                                                        </div>
                                                        <div class="col-md-8 col-12">
                                                            <div class="d-flex gap-2 flex-wrap">
                                                                @foreach(['read'] as $perm)
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input"
                                                                            type="checkbox"
                                                                            id="menu_{{ $child->id }}_{{ $perm }}"
                                                                            name="menu_access[{{ $child->id }}][]"
                                                                            value="{{ $perm }}"
                                                                            {{ isset($menu_access[$child->id][$perm]) && $menu_access[$child->id][$perm] ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="menu_{{ $child->id }}_{{ $perm }}">
                                                                            {{ ucfirst($perm) }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <center>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-success" type="submit">
                                            <li class="bx bx-save"></li> Submit
                                        </button>
                                        <a class="btn btn-sm btn-danger" href="{{ route('user-admin') }}">
                                            <li class="bx bx-arrow-back"></li> Kembali
                                        </a>
                                    </div>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- FORM END -->
@endsection

@section('script')
    <script>
        $(document).on('click', '.delete-form', function () {
            let id = $(this).attr('data-id');
            $('#id_delete').val(id);
        });
    </script>
@endsection