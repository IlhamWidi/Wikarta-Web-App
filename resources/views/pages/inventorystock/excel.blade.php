<html>

<body>
    <table style="border:0px solid #bfbfbf;width:100%;font-size: 10px;font-family: 'Poppins', sans-serif;">
        <tr>
            <td style="width: 50px;text-align:center;">NO</td>
            <td style="width: 150px;vertical-align:middle;">TANGGAL</td>
            <td style="width: 250px;vertical-align:middle;">PRODUCT</td>
            <td style="width: 150px;vertical-align:middle;">CHANNEL</td>
            <td style="width: 150px;vertical-align:middle;">HARGA</td>
            <td style="width: 150px;vertical-align:middle;">QTY</td>
        </tr>
        @if(isset($data) && count($data)>0)
        @foreach($data as $k => $v)
        <tr>
            <td>{{ $k+1 }}</td>
            <td>{{ date('d-m-Y', strtotime($v->created_at))}}</td>
            <td>{{ $v->products->name ?? '-' }}</td>
            <td>{{ $v->channel ?? '-' }}</td>
            <td>{{ $v->price ?? '-' }}</td>
            <td>{{ $v->qty ?? '-' }}</td>
        </tr>
        @endforeach
        @endif
    </table>
</body>

</html>