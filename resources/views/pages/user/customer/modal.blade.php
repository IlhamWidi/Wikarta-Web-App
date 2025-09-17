<!-- Modal Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('user-customer.delete') }}" method="post">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <li class="bx bx-trash"></li>&nbsp;
                    <h6 class="modal-title" id="myExtraLargeModalLabel">Delete Confirmation</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="d-flex align-items-center flex-wrap">
                        <input type="hidden" id="id_delete" name="id" />
                        <div class="text-xl text-dark font-weight-bolder text-center">
                            Anda yakin menghapus data ini ?
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <a href="javascript:void(0);" class="btn btn-sm btn-warning" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1 align-middle"></i> Close</a>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <li class="bx bx-trash me-1 align-middle"></li> Delete
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>