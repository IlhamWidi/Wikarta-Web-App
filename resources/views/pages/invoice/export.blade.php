<!DOCTYPE html>
<html>

<head>
    <title>DOKUMEN INVOICE</title>
    <style type="text/css">
        table {
            width: 100%;
        }

        table#content {
            font-family: 'arial';
            font-size: 14px;
            border: 1px solid;
            border-collapse: collapse;
        }

        table#content2 {
            font-family: 'arial';
            font-size: 14px;
            border: 1px solid;
            border-collapse: collapse;
        }

        table,
        tr,
        td {
            vertical-align: top;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
        }

        .float-r {
            float: right
        }

        .italic {
            font-style: italic;
        }

        .fs-10 {
            font-size: 10px
        }

        .fs-12 {
            font-size: 13px
        }

        .fs-14 {
            font-size: 14px
        }

        .rst-child,
        .table-lab td:nth-child(4) {
            width: 13%;
        }

        .table-lab td:nth-child(2),
        .table-lab td:nth-child(5) {
            width: 2%;
        }

        .table-lab td:nth-child(3) {
            width: 40%;
        }

        .table-lab td:nth-child(6) {
            width: 25%;
        }

        .table-grid,
        .table-grid td {
            border-top: 0.1em solid #333;
            border-collapse: collapse;
            padding: 3px;
        }

        .t-center {
            text-align: center;
        }

        .my-2 {
            margin-top: 20px
        }

        .fw-bold {
            font-weight: bold;
        }

        hr {
            margin: 0
        }

        .table-footer td:nth-child(1) {
            width: 35%;
        }

        .table-footer td:nth-child(2) {
            width: 35%;
        }

        .table-footer td:nth-child(3) {
            width: 30%;
        }
    </style>
</head>

<body>
    <table style="margin-bottom: 1em!important;">
        <tr>
            <td rowspan="2" style="width: 20%;">
                <img src="{{ asset('favicon.png') }}" width="80%">
            </td>
            <td style="width: 40%;text-align:left;">
                <h4 style="color:midnightblue;">
                    WIKARTA by PT. Lintas Daya Nusantara
                    <br>
                </h4>
                <p class="fs-12">
                    Jalan Griya Kebraon Barat V BE No.12<br>
                    Kel. Kebraon,Kec. Karang Pilang, Kota Surabaya<br>
                    Tlpn.+62 821-1263-6241 <br>
                    Email. cs@wikarta.co.id<br>
                </p>
            </td>
            <td style="width: 40%;text-align:right;">
                <h1>
                    INVOICE<br>
                </h1>
                <br>
                <table style="width: 100%;" class="fs-12">
                    <tr>
                        <td style="width: 60%;">TANGGAL</td>
                        <td style="width: 40%;text-align:right;">{{ date('d-m-Y', strtotime($data->created_at)) }}</td>
                    </tr>
                    <tr>
                        <td style="width: 60%;">NOMOR</td>
                        <td style="width: 40%;text-align:right;">#{{ $data->invoice_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 60%;">STATUS</td>
                        <td style="width: 40%;text-align:right;color:green;font-weight:bold;">LUNAS</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="fs-12" style="width: 100%;">
        <tr>
            <td style="width: 40%;background-color:navy;">
                <h3 style="color: white;">PELANGGAN</h3>
            </td>
            <td style="width: 60%;">
                <h3></h3>
            </td>
        </tr>
    </table>
    <table class="fs-12" style="width: 100%;">
        <tr>
            <td style="width: 40%;">
                {{ $data->users->name ?? '' }}<br>
                {{ $data->users->address ?? '' }}<br>
                Tel : {{ $data->users->phone_number ?? '' }}
            </td>
            <td style="width: 60%;">
            </td>
        </tr>
    </table>
    <br>
    <table id="content" style="border:1px solid black;width:100%;">
        <thead style="background-color:navy;">
            <tr style="color: white;">
                <th style="border:1px solid black;width:30%;text-align:left;">&nbsp;DESKRIPSI</th>
                <th style="border:1px solid black;width:20%;text-align:center;">&nbsp;HARGA (Rp.)</th>
                <th style="border:1px solid black;width:10%;text-align:center;">&nbsp;JUMLAH</th>
                <th style="border:1px solid black;width:20%;text-align:left;">&nbsp;SUBTOTAL (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border:1px solid black;width:20%;text-align:left;">
                    {{ $data->invoice_description }}
                </td>
                <td style="border:1px solid black;width:20%;text-align:right;">
                    Rp. {{ number_format($data->amount) }}
                </td>
                <td style="border:1px solid black;width:20%;text-align:center;">
                    1
                </td>
                <td style="border:1px solid black;width:20%;text-align:right;">
                    Rp. {{ number_format($data->amount) }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table style="width: 100%;" class="fs-14">
        <tr>
            <td style="width: 50%;">
                <table id="content2" style="border:1px solid black;width:100%;">
                    <tr style="background-color: navy;color:white;">
                        <th style="border:1px solid black;text-align:left;">&nbsp;INFORMASI/CATATAN</th>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;margin:2px;">
                            <br>
                            Harga sudah termasuk ppn 11%
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:10%; text-align: right;"></td>
            <td style="width:40%; text-align: right;">
                <table id="content2" style="width:100%;border:none;border-collapse: collapse;">
                    <tr style="margin-bottom: 2px;border-bottom: 1pt solid black;">
                        <td style="margin:2px;">
                            TOTAL
                        </td>
                        <td style="text-align:right;"> Rp. {{ number_format($data->amount) }}
                        </td>
                    </tr>
                    <tr style="margin-bottom: 2px;border-bottom: 1pt solid black;">
                        <td style="margin:2px;">
                            DISKON
                        </td>
                        <td style="text-align:right;">Rp. 0</td>
                    </tr>
                    <tr style="margin-bottom: 2px;border-bottom: 1pt solid black;">
                        <td style="margin:2px;">
                            <b>SISA TAGIHAN</b>
                        </td>
                        <td style="text-align:right;"><b> Rp. {{ number_format($data->amount) }}</b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;" class="fs-14">
        <tr>
            <td style="width: 50%;">

            </td>
            <td style="width:50%; text-align: right;">
                Salam Hangat,
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                Admin Wiyaja Karya Arta
            </td>
        </tr>
    </table>
</body>

</html>