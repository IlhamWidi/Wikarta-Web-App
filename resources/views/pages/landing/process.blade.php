<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <title>PT. Wijaya Karya Arta</title>
    <meta content="Wijaya Karya Arta Labs" name="author" />
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&amp;family=Inter:wght@400;500;600&amp;family=Nunito:wght@400;500;600&amp;family=Open+Sans:wght@400;500;600&amp;family=Playfair+Display:wght@400;500;600&amp;family=Questrial:wght@400;500;600&amp;family=Roboto:wght@400;500;600&amp;family=Source+Sans+Pro:wght@400;500;600&amp;display=swap">
    <link href="https://d2f3dnusg0rbp7.cloudfront.net/snap/v4/assets/main.redirection.sandbox.4be3481b1e21ea75c7b7.css" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div id="application" class="app-container">
            <nav class="header" id="header">
                <div class="title-bar" style="background-color:darkslateblue;">
                    <div class="logo-store">
                        <div class="merchant-name">WIKARTA - {{ $method->description ?? '' }}</div>
                    </div>
                </div>
                <div class="order-header">
                    <div class="order-box">
                        <div class="order-summary-section">
                            <div class="order-container">
                                <div class="header-amount">Rp {{ isset($invoice->amount) ? number_format($invoice->amount) : 0 }}</div>
                            </div>
                            <div class="order-container">
                                <div class="header-order-id-wrapper">
                                    <p class="header-order-id">Bayar sebelum {{ isset($invoice->expired_at) ? date('d-m-Y H:i:s', strtotime($invoice->expired_at)) : '-' }} </p>
                                </div>
                                <div class="header-detail-clickable">
                                    <div class="header-copy-icon">
                                        <svg height="16" width="16" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15,0 L5,0 C4.45,0 4,0.45 4,1 L4,3 L6,3 L6,2 L14,2 L14,9 L13,9 L13,11 L15,11 C15.55,11 16,10.55 16,10 L16,1 C16,0.45 15.55,0 15,0 Z M11,4 L1,4 C0.45,4 0,4.45 0,5 L0,15 C0,15.55 0.45,16 1,16 L11,16 C11.55,16 12,15.55 12,15 L12,5 C12,4.45 11.55,4 11,4 Z M10,14 L2,14 L2,6 L10,6 L10,14 Z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="header-timer-wrapper">
                            <div class="expiry-countdown-label" style="color: red;"><span class="expiry-countdown-timer" id="demo">00:00:00</span></div>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="page-container scroll">
                <div class="payment-page">
                    <div>
                        <div class="page-title"><span class="title-text text-actionable-bold">{{ $method->bank_name ?? '' }}</span>
                            <div class="flex-right"><span><img class="logo page-logo" style="width: 80px!important;" src="{{ asset($method->photo) ?? '' }}" alt="titleLogo"></span></div>
                        </div>
                        @if($method->midtrans_code == "bank_transfer")
                        <div class="payment-page-layout payment-page-text">
                            <div>Nomor Virtual Account</div>
                            <div>
                                <input type="hidden" value="{{ $invoice->va_number }}" id="nomor_rekening" />
                                <div class="payment-number" id="billerCodeField">{{ $invoice->va_number }}</div>
                                <!-- <a class="float-right clickable copy" id="button-copy-rekening">Copy</a> -->
                            </div>
                            <hr class="separator-line">
                        </div>
                        @elseif($method->midtrans_code == "echannel")
                        <div class="payment-page-layout payment-page-text">
                            <div>Company Code</div>
                            <div>
                                <input type="hidden" value="{{ $amount  ?? 0 }}" id="amount" />
                                <div class="payment-number" id="vaField">{{ $invoice->va_key }}</div>
                                <!-- <a class="float-right clickable copy" id="button-copy-amount">Copy</a> -->
                            </div>
                            <hr class="separator-line">
                        </div>
                        <div class="payment-page-layout payment-page-text">
                            <div>Nomor Virtual Account</div>
                            <div>
                                <input type="hidden" value="{{ $invoice->va_number }}" id="nomor_rekening" />
                                <div class="payment-number" id="billerCodeField">{{ $invoice->va_number }}</div>
                                <!-- <a class="float-right clickable copy" id="button-copy-rekening">Copy</a> -->
                            </div>
                            <hr class="separator-line">
                        </div>
                        @elseif($method->midtrans_code == "gopay")
                        <div class="qr-wrapper">
                            <img class="qr-image" src="https://api.sandbox.midtrans.com/v2/qris/8344cf70-fb3f-4138-a821-9324411742f8/qr-code" alt="qr-code">
                        </div>
                        @endif

                        @if($method->midtrans_code == "gopay")
                        <div class="payment-page-layout payment-page-text bank-transfer-page">
                            <div class="collapsible pay-instruction">
                                <div class="header-order-id" style="margin-bottom: 1rem;">
                                    Silahkan scan QR Code diatas menggunakan aplikasi go-pay atau qr banking anda.
                                </div>
                            </div>
                            <hr class="separator-line no-margin">
                        </div>
                        @else
                        <div class="payment-page-layout payment-page-text bank-transfer-page">
                            <div class="collapsible pay-instruction">
                                <div class="header-order-id" style="margin-bottom: 1rem;">
                                    Silahkan buka aplikasi m-banking anda lalu masukkan nomor va diatas. setelah itu lakukan pembayaran.
                                </div>
                            </div>
                            <hr class="separator-line no-margin">
                            <a href="{{ route('landing.payment', ['id' => $id])  }}" type="button" class="btn-primary btn full primary  transparent get-status-button-group--transparent" style="color: red;">Kembali</a>
                        </div>
                        @endif
                    </div>
                    <div>
                        <div class="card-pay-button-part">
                            <div class="get-status-button-group">
                                <!-- <button type="button" class="btn full primary  btn-theme">Check status</button> -->
                                <!-- <a href="{{ route('landing.payment', ['id' => $id])  }}" type="button" class="btn-primary btn full primary  transparent get-status-button-group--transparent">Kembali</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        var timer2 = '<?= $duration  ?? "01:00:00"; ?>';
        setInterval(function() {
            var timer = timer2.split(':');
            var hours = parseInt(timer[0], 10);
            var minutes = parseInt(timer[1], 10);
            var seconds = parseInt(timer[2], 10);

            if (hours > 0 && minutes == 0) {
                minutes = 60;
                --hours;
            }

            if (minutes > 0 && seconds == 0) {
                seconds = 60;
                minutes--;
            }
            // seconds = (seconds > 0) ? 59 : seconds;
            if (seconds > 0)
                --seconds;

            seconds = (seconds < 10) ? '0' + seconds : seconds;
            minutes = (minutes < 10) ? '0' + minutes : minutes;
            hours = (hours < 10) ? '0' + hours : hours;

            document.getElementById("demo").innerHTML = hours + ':' + minutes + ':' + seconds;
            timer2 = hours + ':' + minutes + ':' + seconds;
            if (timer2 == "00:00:00") document.getElementById("demo").innerHTML = "EXPIRED"
        }, 1000);

        $("#button-copy-amount").click(function() {
            $("#amount").select();
            navigator.clipboard.writeText($("#amount").val());
        })

        $("#button-copy-rekening").click(function() {
            $("#nomor_rekening").select();
            navigator.clipboard.writeText($("#nomor_rekening").val());
        })
    </script>

</body>

</html>