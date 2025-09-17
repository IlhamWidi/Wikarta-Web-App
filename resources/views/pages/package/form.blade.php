<!-- FORM START -->
<form method="post" action="{{ isset($one) ? route('package.update', ['id' => $one->id]) : route('package.store') }}">@csrf
    <div class="row">
        <div class="col-xl-12" id="card-none1">
            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-edit"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Form Paket</span>
                        </div>
                        <div class="flex-shrink-0">
                        </div>
                    </div>
                </div>
                <div class="card-body collapse show" id="collapseexample1">
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="basiInput" class="form-label mb-0">Name</label>
                            <input name="name" type="text" class="form-control" value="{{ $one->name ?? old('name') }}">
                            <label for="basiInput" class="form-label mb-0">Description</label>
                            <input name="description" type="text" class="form-control" value="{{ $one->description ?? old('description') }}">
                            <div class="form-check form-switch form-switch-md mb-3 mt-3" dir="ltr">
                                <label class="form-check-label" for="customSwitchsizemd">Publish</label>
                                <input name="publish" type="checkbox" class="form-check-input" id="customSwitchsizemd" {{ isset($one->publish) && $one->publish == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="subscribe_price" class="form-label mb-0">Subscribe (Fee)</label>
                            <div class="form-icon right">
                                <input name="subscribe_price" type="text" class="form-control form-control-icon autonumber" id="subscribe_price" value="{{ $one->subscribe_price ?? old('subscribe_price') }}">
                            </div>
                            <label for="registration_price" class="form-label mb-0">Registration (Fee)</label>
                            <div class="form-icon right">
                                <input name="registration_price" type="text" class="form-control form-control-icon autonumber" id="registration_price" value="{{ $one->registration_price ?? old('registration_price') }}">
                            </div>
                            <label for="sequence" class="form-label mb-0">Sequence (Urutan)</label>
                            <div class="form-icon right">
                                <input name="sequence" type="number" class="form-control form-control-icon" id="sequence" value="{{ $one->sequence ?? old('sequence') }}">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="exampleFormControlTextarea5" class="form-label mb-0">Spesification (Sparated by Comma)</label>
                            <textarea name="spesification" style="max-height: 105px;" class="form-control" id="exampleFormControlTextarea5" rows="5">{{ $one->spesification ?? old('spesification') }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <center>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <li class="bx bx-save"></li> Submit
                                    </button>
                                    <a class="btn btn-sm btn-danger" href="{{ route('package') }}">
                                        <li class="bx bx-x"></li> Reset
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