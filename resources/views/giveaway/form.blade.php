<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if(isset($giveaway))
    @method('PUT')
    @endif

    <!-- ...existing code... -->

    <div class="mb-3">
        <label for="recipient_photo" class="form-label">Foto Penerima</label>
        <input type="file" class="form-control @error('recipient_photo') is-invalid @enderror"
            id="recipient_photo" name="recipient_photo">
        @if(isset($giveaway) && $giveaway->recipient_photo)
        <div class="mt-2">
            <img src="{{ asset($giveaway->recipient_photo) }}" alt="Foto Penerima" class="img-thumbnail" width="200">
        </div>
        @endif
        @error('recipient_photo')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- ...existing code... -->
</form>