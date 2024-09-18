<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Label Delivery Note PT Sanoh Indonesia</title>
    <link rel="icon" type="image/png" href="../../assets/icon_sanoh.png">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            box-sizing: border-box;
            page-break-inside: avoid;
            font-size: 9px;  /* Ukuran font dikurangi */
        }

        .page {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 0;
            box-sizing: border-box;
            margin: 0;
        }

        .header-text {
            font-size: 4px;
            text-align: left;
            height: 25px;
            margin-bottom: 8px;
            line-height: 1;
        }

        .model-label, .customer-label {
            font-size: 7px;
            font-weight: normal;
            text-align: left; /* Sejajarkan teks di kanan */
            display: inline-block;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .label {
            width: 50%;
            height: auto;
            border: 1px dashed rgb(144, 143, 143);
            box-sizing: border-box;
            margin: 0;
            padding: 2px;  /* Padding dikurangi */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table td, .table th {
            border: 1px solid rgb(162, 162, 162);
            padding: 2px;  /* Padding dikurangi */
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
        }

        .table td:nth-child(1), .table th:nth-child(1) {
            width: 4%;  /* Lebar dikurangi */
            height: 20px;  /* Tinggi dikurangi */
        }

        .table td:nth-child(2), .table th:nth-child(2) {
            width: 16%;  /* Lebar dikurangi */
        }

        .table td:nth-child(3), .table th:nth-child(3) {
            width: 10%;  /* Lebar dikurangi */
        }

        .table td:nth-child(4), .table th:nth-child(4) {
            width: 18%;  /* Lebar dikurangi */
        }

        .table td:nth-child(5), .table th:nth-child(5) {
            width: 9%;  /* Lebar dikurangi */
        }

        .table td:nth-child(6), .table th:nth-child(6) {
            width: 9%;  /* Lebar dikurangi */
        }

        .table td:nth-child(7), .table th:nth-child(7) {
            width: 11%;  /* Lebar dikurangi */
        }

        .right-text {
            text-align: right;
        }

        .left-text {
            text-align: left;
        }

        .part-no-container {
            display: flex;
            align-items: flex-end;
        }

        .part-no-label {
            font-size: 7px;  /* Ukuran font dikurangi */
            text-align: center;
            font-weight: normal;
            margin-right: 5px;  /* Margin dikurangi */
            margin-bottom: 20px;  /* Margin bawah dikurangi */
        }

        .part-no-value {
            font-size: 11px;  /* Ukuran font dikurangi */
            font-weight: bold;
            margin-top: 8px;  /* Margin atas dikurangi */
            line-height: 1;
        }

        .part-name-container {
            display: flex;
            align-items: flex-end;
            height: 25px;  /* Tinggi dikurangi */
            margin-bottom: 2px;  /* Margin bawah dikurangi */
            padding: 0;
        }

        .part-name-label {
            font-size: 7px;  /* Ukuran font dikurangi */
            text-align: center;
            font-weight: normal;
            margin-right: 5px;  /* Margin dikurangi */
            margin-bottom: 1px;  /* Margin bawah dikurangi */
            line-height: 3.5;
        }

        .part-name-value {
            font-size: 11px;  /* Ukuran font dikurangi */
            text-align: center;
            font-weight: bold;
            margin-top: 5px;  /* Margin atas dikurangi */
            line-height: 1;
        }

        .lot-no-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .lot-no-label {
            font-size: 7px;  /* Ukuran font dikurangi */
            font-weight: normal;
            text-align: left;
            line-height: 1.5;
            border: none;
            background: none;
            padding: 0;
            margin-bottom: 15px;  /* Margin bawah dikurangi */
        }

        .lot-no-value {
            font-size: 10px;  /* Ukuran font dikurangi */
            font-weight: bold;
            text-align: right;
            letter-spacing: 0;
            margin-top: 20px;  /* Margin atas dikurangi */
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .quality-label {
            font-size: 7px;
            font-weight: normal;
            text-align: left;
            display: inline-block;
            margin-bottom: 20px; /* Mengatur margin bawah menjadi 20px */
        }

        .inspection-label {
            font-size: 7px;
            font-weight: normal;
            text-align: center;
            display: inline-block;
            margin-bottom: 20px; /* Mengatur margin bawah menjadi 20px */
        }

        .header-text {
            padding: 0; /* Menghapus padding default jika ada */
            margin-bottom: 0; /* Pastikan margin bawah di header-text adalah 0 untuk menghindari konflik */
        }

        .center-text {
            text-align: center; /* Memastikan teks tetap di tengah secara horizontal */
        }

        .printed-date-label {
            font-size: 8px;  /* Ukuran font dikurangi */
            text-align: left;
            border: none;
        }

        .printed-date-value {
            font-size: 5px;  /* Ukuran font dikurangi */
            color: #555;
            text-align: left;
            border: none;
            margin-bottom: 8px;

        }

        .quantity-container {
            display: flex;
            padding: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            height: 100%;
            margin-bottom: 0;
        }

        .quantity-label {
            padding: 0;
            height: 3px;
            font-size: 7px;  /* Ukuran font dikurangi */
            font-weight: normal;
            text-align: left;
            margin-bottom: 0;
            width: 100%;
        }

        .quantity-value {
            font-size: 13px;  /* Ukuran font dikurangi */
            font-weight: bold;
            text-align: center;
            letter-spacing: 0;
            margin-bottom: 5px;

        }

        /* Mengurangi padding dan line-height */
        .company-name {
            font-size: 10px;  /* Ukuran font */
            font-weight: bold;
            text-align: center;  /* Atur teks menjadi center */
            padding: 0;
            height: 10px;  /* Tinggi */
            margin-top: 1px;
            display: flex;   /* Tambahkan display flex */
            justify-content: center; /* Pusatkan secara horizontal */
            align-items: center; /* Pusatkan secara vertikal */
        }

        .pl-number {
            font-size: 9px;  /* Ukuran font */
            font-weight: bold;
            text-align: center;  /* Atur teks menjadi center */
            display: block;
            margin-top: 10px;
            margin-bottom: 2px;
        }

        .delivery-date-label {
            font-size: 7px;
            font-weight: normal;
            text-align: left;  /* Label diatur ke kiri */
            margin-bottom: 0;
            line-height: 1.1;
            display: block;  /* Pastikan label menggunakan lebar penuh */
        }

        .delivery-date-value {
            font-size: 10px;
            font-weight: bold;
            text-align: center;  /* Nilai diatur ke tengah */
            margin-bottom: 5px;
            line-height: 1;
            display: block;  /* Pastikan nilai menggunakan lebar penuh */
        }

        .qr-code-container {
            display: flex;
            justify-content: space-between;
            padding: 0;
        }

        .qr-code {
            display: flex;
            align-items: center;
            margin-left: 0;
            padding: 2px;  /* Padding dikurangi */
        }

        .qr-code-right {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            margin-left: 5px;  /* Margin dikurangi */
            padding: 2px;  /* Padding dikurangi */
        }

        .qr-code span {
            font-size: 7px;  /* Ukuran font dikurangi */
            margin-left: 10px;  /* Memberikan jarak antara QR code dan teks */
        }

        .qr-code-right span {
            font-size: 7px;  /* Ukuran font dikurangi */
            text-align: center;
            margin-left: 30px;  /* Margin dikurangi */
            margin-top: 3px;  /* Margin dikurangi */
            margin-right: 30px;  /* Margin dikurangi */
        }

        .footer {
            font-size: 0.8em;
            text-align: center;
            padding: 5px;
        }

        .date {
            font-size: 1.2em;
            text-align: center;
        }

        @media print {
            .label {
                border: 1px dashed rgb(107, 105, 105);
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>

    <div class="page" id="page-content"></div>

<script>
// Render table content for the purchase order
function renderLabel(header, items) {
    let pageContent = '';

    items.forEach((item, index) => {
        pageContent += `
        <div class="label">
            <table class="table">
                <tr>
                    <td style="font-size: 5px !important; line-height: 1 !important;">
                        <span class="model-label">MODEL</span>
                    </td>
                    <td colspan="5" class="header-text">
                        <div class="part-no-container">
                            <span class="part-no-label">PART NO</span>
                            <span class="part-no-value">{{ $item['part_number'] }}</span>
                        </div>
                    </td>
                    <td class="header-text left-text">
                        <div class="lot-no-container">
                            <span class="lot-no-label">LOT NO</span>
                            <span class="lot-no-value">{{ $item['lot_number'] }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="header-text">
                    <span class="customer-label">CUSTOMER</span>
                    </td>
                    <td colspan="5" class="header-text">
                        <div class="part-name-container">
                            <span class="part-name-label">PART NAME</span>
                            <span class="part-name-value">{{ $item['part_name'] }}</span>
                        </div>
                    </td>
                    <td class="header-text">
                        <div class="quantity-container">
                            <span class="quantity-label">QUANTITY</span><br>
                            <span class="quantity-value">{{ $item['quantity'] }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <span class="company-name">{{ $item['customer_name'] }}</span>
                    </td>
                    <td colspan="2" class="header-text">
                        <span class="pl-number">{{ $item['po_number'] }}</span>
                    </td>
                    <td class="header-text right-text">
                        <span class="delivery-date-label" style="line-height: 0.8; margin: 0;">DATE DELIVERY</span><br>
                        <span class="delivery-date-value" style="line-height: 0.8; margin: 0;">{{ $item['delivery_date'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="header-text">
                        <span class="quality-label">QUALITY</span>
                    </td>
                    <td colspan="3" class="header-text">
                        <span class="inspection-label">INSPECTION</span>
                    </td>
                    <td colspan="2" class="header-text left-text no-border">
                        <span class="printed-date-label">PRINTED DATE</span><br>
                        <span class="printed-date-value">{{ $item['printed_date'] }}</span>
                </tr>
                <tr>
                    <td colspan="7">
                        <div class="qr-code-container">
                            <div class="qr-code" style="margin-left: 13px;">
                                <div id="qrcode-left-{{ $index }}"></div>
                                <span>{{ $item['qr_number'] }}</span>
                            </div>
                            <div class="qr-code-right">
                                <div id="qrcode-right-{{ $index }}"></div>
                                <span>{{ $item['po_number'] }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>`;
    });

    document.getElementById('page-content').innerHTML = pageContent;

    // Generate QR Codes for each item after rendering the page
    items.forEach((item, index) => {
        generateQRCodes(item['qr_number'], item['po_number'], index);
    });
}

// Generate QR codes for each label
function generateQRCodes(qrNumber, poNumber, index) {
    new QRCode(document.getElementById(`qrcode-left-${index}`), {
        text: qrNumber,
        width: 50,
        height: 50,
        correctLevel: QRCode.CorrectLevel.L,
    });

    new QRCode(document.getElementById(`qrcode-right-${index}`), {
        text: poNumber ? poNumber : "No PO Number",
        width: 40,
        height: 40,
        correctLevel: QRCode.CorrectLevel.L,
    });
}
</script>
</body>
</html>
