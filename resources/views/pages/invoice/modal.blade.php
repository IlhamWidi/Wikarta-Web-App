  <!--  MODAL 1 START -->
  <div class="modal fade bs-example-modal-xl pt-5" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
          <form method="post" action="{{ route('invoice.update') }}">@csrf
              <div class="modal-content">
                  <div class="modal-header">
                      <li class="bx bx-accessibility"></li>&nbsp;
                      <h6 class="modal-title" id="myExtraLargeModalLabel"> Form Edit Invoice</h6>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <hr class="mt-2 mb-2">
                  <div class="modal-body">
                      <div class="row">
                          <div class="col-lg-4">
                              <input type="hidden" name="id" id="id_edit" />
                              <label for="invoice_number" class="form-label mb-0">Invoice No</label>
                              <input type="text" class="form-control" disabled id="invoice_number">

                              <label for="customer" class="form-label mb-0">Customer</label>
                              <input type="text" class="form-control" disabled id="customer">

                              <label for="branch" class="form-label mb-0">Branch</label>
                              <div class="form-icon right">
                                  <input type="text" class="form-control" disabled id="branch">
                              </div>
                          </div>
                          <div class="col-lg-4">
                              <label for="due_date" class="form-label mb-0">Due Date</label>
                              <div class="form-icon right">
                                  <input type="text" class="form-control" id="due_date" disabled>
                              </div>
                              <label for="amount" class="form-label mb-0">Amount</label>
                              <div class="form-icon right">
                                  <input type="text" class="form-control" id="amount" disabled>
                              </div>
                              <label for="description" class="form-label mb-0">Description</label>
                              <div class="form-icon right">
                                  <textarea class="form-control" disabled id="description"></textarea>
                              </div>
                          </div>
                          <div class="col-lg-4">
                              <label for="choices-single-default" class="form-label mb-0">Payment Method</label>
                              <select class="form-control js-example-basic-single" id="payment_method" name="payment_method">
                                  <option value="">Select</option>
                                  @if(isset($ms_payment_methods))
                                  @foreach($ms_payment_methods as $x)
                                  <option value="{{ $x }}">{{ $x }}</option>
                                  @endforeach
                                  @endif
                              </select>
                              <label for="choices-single-default" class="form-label mb-0">Status</label>
                              <select class="form-control js-example-basic-single" id="invoice_status" name="invoice_status">
                                  <option value="">Select</option>
                                  @if(isset($ms_invoice_statuses))
                                  @foreach($ms_invoice_statuses as $x)
                                  <option value="{{ $x }}">{{ $x }}</option>
                                  @endforeach
                                  @endif
                              </select>
                          </div>
                      </div>
                  </div>
                  <hr class="mt-2 mb-2">
                  <div class="modal-footer">
                      <div class="btn-group">
                          <a href="javascript:void(0);" class="btn btn-sm btn-warning" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                          <button type="submit" class="btn btn-sm btn-success">
                              <li class="bx bx-save me-1 align-middle"></li> Submit
                          </button>
                      </div>
                  </div>
              </div>
          </form>
      </div>
  </div>
  <!--  MODAL 1 END -->