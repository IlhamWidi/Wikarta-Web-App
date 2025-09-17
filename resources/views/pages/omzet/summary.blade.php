<!-- APPOINTMENT COUNTER  START -->
<div class="row">
    <div class="col-lg-4">

        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Omzet Cash</p>
                    </div>
                </div>

                <div class="d-flex align-items-end justify-content-between mt-3">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-3"><span>Rp.{{ number_format($response['cash']) }}</span>
                        </h4>
                        <a href="" class="">Omzet Cash</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-primary rounded fs-3">
                            <i class="bx bx bx-file text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Omzet Midtrans</p>
                    </div>
                </div>

                <div class="d-flex align-items-end justify-content-between mt-3">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-3"><span>Rp.{{ number_format($response['midtrans']) }}</span>
                        </h4>
                        <a href="" class="">Omzet Midtrans</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-success rounded fs-3">
                            <i class="bx bx bx-file text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Omzet Total</p>
                    </div>
                </div>

                <div class="d-flex align-items-end justify-content-between mt-3">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-3"><span>Rp.{{ number_format($response['total']) }}</span>
                        </h4>
                        <a href="" class="">Omzet Total</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-danger rounded fs-3">
                            <i class="bx bx bx-file text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- APPOINTMENT COUNTER  END -->