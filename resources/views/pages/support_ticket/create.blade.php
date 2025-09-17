@extends('layouts.main')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa fa-ticket-alt me-2"></i>Tambah Tiket Support</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('support_ticket.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Cari Customer (Nama/No Telp)</label>
                            <input type="text" id="search-customer" class="form-control" placeholder="Nama atau No Telp">
                            <input type="hidden" name="customer_id" id="customer_id">
                        </div>
                        <div id="customer-detail" style="display:none;">
                            <div class="row g-2">
                                <div class="col-md-6 mb-3"><label class="form-label">Cabang</label><input type="text"
                                        id="branch" class="form-control" readonly></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Paket</label><input type="text"
                                        id="package" class="form-control" readonly></div>
                            </div>
                            <div class="mb-3"><label class="form-label">Alamat</label><input type="text" id="address"
                                    name="address" class="form-control" readonly></div>
                            <div class="row g-2">
                                <div class="col-md-6 mb-3"><label class="form-label">Kode Server</label><input type="text"
                                        id="kode_server" name="kode_server" class="form-control" readonly></div>
                                <div class="col-md-6 mb-3"><label class="form-label">Password Server</label><input
                                        type="text" id="password_server" name="password_server" class="form-control"
                                        readonly></div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4 mb-3"><label class="form-label">VLAN</label><input type="text"
                                        id="vlan" name="vlan" class="form-control" readonly></div>
                                <div class="col-md-4 mb-3"><label class="form-label">ODP</label><input type="text" id="odp"
                                        name="odp" class="form-control" readonly></div>
                                <div class="col-md-4 mb-3"><label class="form-label">ODC</label><input type="text" id="odc"
                                        name="odc" class="form-control" readonly></div>
                            </div>
                            <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" id="keterangan"
                                    name="keterangan" class="form-control" readonly></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea name="keluhan" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PIC</label>
                            <select name="teknisi_id" class="form-select" required>
                                <option value="">Pilih PIC</option>
                                @foreach($teknisi as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Foto Keluhan</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save me-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('search-customer').addEventListener('blur', function () {
            let q = this.value;
            if (!q) return;
            fetch('/support-ticket/search-customer?q=' + encodeURIComponent(q))
                .then(res => res.json())
                .then(data => {
                    if (data.error) { alert(data.error); return; }
                    document.getElementById('customer_id').value = data.customer.id;
                    document.getElementById('branch').value = data.branch ? data.branch.name : '';
                    document.getElementById('package').value = data.package ? data.package.name + '-' + data.package.description : '';
                    document.getElementById('address').value = data.address;
                    document.getElementById('kode_server').value = data.kode_server;
                    document.getElementById('password_server').value = data.password_server;
                    document.getElementById('vlan').value = data.vlan;
                    document.getElementById('odp').value = data.odp;
                    document.getElementById('odc').value = data.odc;
                    document.getElementById('keterangan').value = data.keterangan;
                    document.getElementById('customer-detail').style.display = '';
                });
        });
    </script>
@endsection