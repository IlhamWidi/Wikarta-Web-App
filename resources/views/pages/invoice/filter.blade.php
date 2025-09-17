<!-- FORM START -->
<div class="row">
    <div class="col-xl-12" id="card-none1">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-search-alt-2"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Search / Filter</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>

            <div class="card-body collapse show" id="collapseexample1">
                <form method="post" action="{{ route('invoice') }}">@csrf

                    <div class="row">
                        <div class="col-lg-4">
                            <label for="choices-single-default" class="form-label mb-0">Month</label>
                            <select class="form-control js-example-basic-single" name="month">
                                <option value="">Select</option>
                                @if(isset($ms_months))
                                @foreach($ms_months as $k => $v)
                                <option value="{{ $k }}" <?= isset($request->month) && $request->month == $k ? 'selected=selected' : ''; ?>>{{ $v }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="choices-single-default" class="form-label mb-0">Year</label>
                            <select class="form-control js-example-basic-single" name="year">
                                <option value="">Select</option>
                                @if(isset($ms_years))
                                @foreach($ms_years as $x)
                                <option value="{{ $x }}" <?= isset($request->year) && $request->year == $x ? 'selected=selected' : ''; ?>>{{ $x }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="choices-single-default" class="form-label mb-0">Branch</label>
                            <select class="form-control js-example-basic-single" name="branch">
                                <option value="">Select</option>
                                @if(isset($ms_branches))
                                @foreach($ms_branches as $k => $v)
                                <option value="{{ $v->id }}" <?= isset($request->branch) && $request->branch == $v->id ? 'selected=selected' : ''; ?>>{{ $v->code }}-{{ $v->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <hr class="mt-3 mb-2">

                    <div class="row">
                        <div class="col-lg-12">
                            <center>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <li class="bx bx-search-alt-2"></li> Search
                                    </button>
                                    <a class="btn btn-sm btn-danger" href="{{ route('invoice') }}">
                                        <li class="bx bx-x"></li> Reset
                                    </a>
                                </div>
                            </center>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
<!-- FORM END -->