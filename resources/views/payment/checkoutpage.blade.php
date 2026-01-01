<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Payment Checkout | Wit Exam Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .loading-container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        h2 {
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        p {
            color: #64748b;
        }
    </style>
</head>

<body>
    <div id="loading-screen" class="loading-container">
        <div class="spinner"></div>
        <h2>Initializing Secure Payment</h2>
        <p>Please wait while we connect to the gateway...</p>
        <div id="worldline_embeded_popup"></div>
    </div>

    <!-- Scripts from reference code -->
    <script type="text/javascript" src="https://www.paynimo.com/paynimocheckout/client/lib/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.paynimo.com/Paynimocheckout/server/lib/checkout.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            if (window.paymentInitialized) {
                console.log('Payment already initialized');
                return;
            }
            window.paymentInitialized = true;

            var transactionId = '{{ $payval['txnId'] }}';
            console.log('Initializing payment for transaction:', transactionId);

            function updateTransactionStatus(status, data = {}) {
                $.ajax({
                    url: '{{ route('student.payment.update-status') }}',
                    method: 'POST',
                    data: {
                        transaction_id: transactionId,
                        status: status,
                        data: JSON.stringify(data),
                        _token: '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.redirect_url) {
                            window.location.replace(response.redirect_url);
                        }
                    }
                }).fail(function (xhr, status, error) {
                    console.error('Failed to update transaction status:', error);
                });
            }

            var configJson = {
                'tarCall': false,
                'features': {
                    'showPGResponseMsg': true,
                    'enableNewWindowFlow': true,
                    'enableAbortResponse': true,
                    'enableExpressPay': {!! $mer_array['enableExpressPay'] == 1 ? 'true' : 'false' !!},
                    'enableInstrumentDeRegistration': {!! $mer_array['enableInstrumentDeRegistration'] == 1 ? 'true' : 'false' !!},
                    'enableMerTxnDetails': true,
                    'siDetailsAtMerchantEnd': {!! $mer_array['enableSIDetailsAtMerchantEnd'] == 1 ? 'true' : 'false' !!},
                    'enableSI': {!! $mer_array['enableEmandate'] == 1 ? 'true' : 'false' !!},
                    'hideSIDetails': {!! $mer_array['hideSIConfirmation'] == 1 ? 'true' : 'false' !!},
                    'enableDebitDay': {!! $mer_array['enableDebitDay'] == 1 ? 'true' : 'false' !!},
                    'expandSIDetails': {!! $mer_array['expandSIDetails'] == 1 ? 'true' : 'false' !!},
                    'enableTxnForNonSICards': {!! $mer_array['enableTxnForNonSICards'] == 1 ? 'true' : 'false' !!},
                    'showSIConfirmation': {!! $mer_array['showSIConfirmation'] == 1 ? 'true' : 'false' !!},
                    'showSIResponseMsg': {!! $mer_array['showSIResponseMsg'] == 1 ? 'true' : 'false' !!}
                },
                'consumerData': {
                    'deviceId': 'WEBSH2',
                    'token': '{{ $payval['hash'] }}',
                    'returnUrl': '{{ route('student.payment.response') }}',
                    'responseHandler': handleResponse,
                    'paymentMode': '{{ $mer_array['paymentMode'] }}',
                    'checkoutElement': '{{ $mer_array['embedPaymentGatewayOnPage'] == 1 ? '#worldline_embeded_popup' : '' }}',
                    'merchantLogoUrl': '{{ $mer_array['logoURL'] }}',
                    'merchantId': '{{ $payval['merchantId'] }}',
                    'currency': '{{ $payval['currencycode'] }}',
                    'consumerId': '{{ $payval['consumerId'] }}',
                    'consumerMobileNo': '{{ $payval['mobileNumber'] }}',
                    'consumerEmailId': '{{ $payval['email'] }}',
                    'txnId': '{{ $payval['txnId'] }}',
                    'items': [{
                        'itemId': '{{ $payval['schemecode'] }}',
                        'amount': '{{ number_format($payval['amount'], 2, '.', '') }}',
                        'comAmt': '0'
                    }],
                    'customStyle': {
                        'PRIMARY_COLOR_CODE': '{{ $mer_array['primaryColor'] }}',
                        'SECONDARY_COLOR_CODE': '{{ $mer_array['secondaryColor'] }}',
                        'BUTTON_COLOR_CODE_1': '{{ $mer_array['buttonColor1'] }}',
                        'BUTTON_COLOR_CODE_2': '{{ $mer_array['buttonColor2'] }}'
                    },
                    'accountNo': '{{ $payval['accNo'] }}',
                    'accountHolderName': '{{ $payval['accountName'] }}',
                    'ifscCode': '{{ $payval['ifscCode'] }}',
                    'accountType': '{{ $payval['accountType'] }}',
                    'debitStartDate': '{{ $payval['debitStartDate'] }}',
                    'debitEndDate': '{{ $payval['debitEndDate'] }}',
                    'maxAmount': '{{ $payval['maxAmount'] }}',
                    'amountType': '{{ $payval['amountType'] }}',
                    'frequency': '{{ $payval['frequency'] }}',
                    'merchantMsg': '{{ $mer_array['merchantMessage'] }}',
                    'disclaimerMsg': '{{ $mer_array['disclaimerMessage'] }}',
                    'saveInstrument': '{{ $mer_array['saveInstrument'] ?? 0 }}',
                    @if (isset($mer_array['paymentModeOrder']))
                        'paymentModeOrder': ['<?php    echo str_replace(',', "','", $mer_array['paymentModeOrder']); ?>']
                    @endif
                }
            };

        console.log('Payment Config:', configJson);

        updateTransactionStatus('pending', {
            event: 'payment_page_loaded',
            timestamp: new Date().toISOString()
        });

        // Standard Paynimo call
        $.pnCheckout(configJson);

        if (configJson.features.enableNewWindowFlow) {
            pnCheckoutShared.openNewWindow();
        }

        function handleResponse(res) {
            console.log('Payment Response received:', res);

            if (window.responseProcessed) {
                console.log('Response already processed');
                return;
            }
            window.responseProcessed = true;

            let stringResponse = res.stringResponse;
            let responseArray = stringResponse.split('|');

            const statusCode = responseArray[0];
            let status = 'pending';
            let redirectUrl = '{{ route('student.payment.pending') }}';

            if (statusCode === '0300') {
                status = 'success';
                redirectUrl = '{{ route('student.payment.success') }}';
            } else if (statusCode === '0398' || statusCode === '0399') {
                status = 'failed';
                redirectUrl = '{{ route('student.payment.failed') }}';
            }

            updateTransactionStatus(status, {
                response: stringResponse,
                event: 'payment_response_received',
                timestamp: new Date().toISOString()
            });

            if (statusCode !== '0300') {
                alert('Payment Status: ' + status + '\n' + stringResponse);
            }

            setTimeout(function () {
                window.location.replace(redirectUrl);
            }, 500);
        }

        // Prevent browser back button
        if (window.history && window.history.pushState) {
            window.history.pushState('forward', null, window.location.href);
            $(window).on('popstate', function () {
                window.history.pushState('forward', null, window.location.href);
            });
        }
        });
    </script>
</body>

</html>