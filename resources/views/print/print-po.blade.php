<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Purchase Order PT Sanoh Indonesia</title>
    <link rel="icon" type="image/png" href="../../assets/icon_sanoh.png">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .container {
            width: 210mm;
            height: auto;
            max-width: 200mm;
            padding: 20px;
            background-color: #fff; /* Latar belakang putih */
            box-sizing: border-box;
            page-break-after: always; /* Pisahkan halaman setelah container */
            position: relative;
        }

        .header, .footer {
            margin-bottom: 10px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .logo img {
            width: 300px;
        }

        .company-info {
            text-align: center;
            flex-grow: 1;
            font-size: 14px;
        }

        .company-info h3 {
            margin: 30px;
            text-decoration: underline;
            text-align: center;
            flex-grow: 5;
            margin-right: 250px;
        }

         /* Page Info: Position at top-right */
         .page-info {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            font-size: 10px;
            margin-bottom: 0;
            margin-top: 0;
            line-height: 1.1;
        }

        .details-left, .details-right {
            text-align: left;
        }

        .details-left div, .details-right .row {
            margin-bottom: 5px;
        }

        .details-left strong, .details-right .label {
            min-width: 15px;
            display: inline-block;
            padding-right: 0px;
        }

        .details-right {
            display: grid;
            grid-template-columns: auto auto;
            justify-content: end;
            text-align: left;
        }

        .details-right .row {
            display: contents;
            margin-bottom: 2px;
            line-height: 1;
        }

        .details-right .label {
            padding-right: 0px; /* Kurangi padding untuk mendekatkan label dengan value */
            min-width: 80px;
            text-align: left;
            font-weight: bold;
            padding-top: 2px;  /* Tambahkan padding atas-bawah jika perlu */
            line-height: 0.5;
        }

        .details-right .value {
            min-width: 100px; /* Kurangi min-width untuk mendekatkan value dengan label */
            text-align: left;
            font-weight: bold;
            line-height: 1;
            padding-left: 0px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px;
            font-size: 7px;
            table-layout: auto;
            text-align: center;
        }

        table, th, td {
            border: 1px solid #e9e6e6;
            padding: 4px 6px;
            word-wrap: break-word;
            white-space: normal;
            text-align: center;
            font-size: 7px; /* Ukuran font diubah menjadi 7px */
        }

        th {
            background-color: #ffffff;
            color: black;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        td {
            vertical-align: middle;
            background-color: #ffffff;
            border: 1px solid #e6e1e1;
            border-bottom: #ffffff;
            border-top: #ffffff;
        }

        table td:nth-child(1) {
            width: 3%;
            font-size: 7px;
            text-align: right;
        }

        table td:nth-child(2) {
            width: 3%;
            font-size: 7px;
            text-align: right;
        }

        table td:nth-child(3) {
            width: 20%;
            font-size: 7px;
            border-left: #ffffff;
            border-right: #ffffff;
            text-align: left;
        }

        table td:nth-child(4) {
            width: 10%;
            font-size: 7px;
            text-align: left;
        }

        table td:nth-child(5) {
            width: 5%;
            font-size: 7px;
            text-align: right;
        }

        table td:nth-child(6) {
            width: 5%;
            font-size: 7px;
            text-align: center;
        }

        table td:nth-child(7) {
            width: 10%;
            font-size: 7px;
            text-align: right;
        }

        table td:nth-child(8) {
            width: 10%;
            font-size: 7px;
            text-align: right;
        }

        .summary-row td {
            border-top: 2px solid #eaeaea;
            font-weight: bold;
            min-width: 10px;
            padding: 8px;
            font-size: 14px;
            text-align: right;
        }

        .summary-row .summary-value {
            background-color: #ffffff; /* Opsional: latar belakang untuk nilai */
            border-top: 2px solid #eaeaea;
            font-size: 9px; /* Ukuran font untuk nilai */
            text-align: right; /* Pastikan nilai rata kanan */
            padding-right: 10px; /* Menambahkan padding kanan untuk memberi jarak */
        }

        .footer {
            margin-top: 20px;
            font-family: Arial, sans-serif;
            font-size: 9px;
        }

        .row {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 2px;
            font-size: 9px;
        }

        .label {
            width: 150px; /* Adjust this width for better alignment */
            text-align: left;
        }

        .value {
            text-align: left;
        }

        .terms {
            margin-top: 20px;
            font-size: 9px;
        }

        .terms div {
            margin-bottom: 5px;
        }

        /* Penyesuaian khusus untuk browser berbasis WebKit (Safari & Chrome) */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .container {
                width: 209.9mm;
                height: 296.9mm;
            }
        }

        /* Penyesuaian khusus untuk Safari */
        @supports (-webkit-touch-callout: none) {
            .container {
                width: 209.9mm; /* Penyesuaian untuk Safari */
                height: 296.9mm; /* Penyesuaian untuk Safari */
            }
        }

        /* Print-specific styles */
        @media print {
            .page-info {
                display: block !important;
            }


            footer {
        display: none;
      }
    .page-break {
        page-break-before: always;
    }
        }

    </style>

</head>

<body>

<!-- Page Content Container -->
<div id="page-content"></div>

<script>
// Function to calculate delivery term
function calculateDeliveryTerm(po_date, planned_receipt_date) {
    const date1 = new Date(po_date);
    const date2 = new Date(planned_receipt_date);
    const diffTime = Math.abs(date2 - date1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Function to concatenate address
function concatAddress(address) {
    return address || 'N/A';
}

// Render table content for the purchase order
function renderTable(data, items) {
    let pageContent = '';
    let rowsPerPage = 16;
    var subtotal = 0;

    items.forEach((item, index) => {
        if (index % rowsPerPage === 0) {
            if (index !== 0) {
                pageContent += `</tbody></table></div>
                <div class="page-break"></div>`;
            }
            pageContent += `
                <div class="container">
                    <div class="header">
                        <div class="logo">
                            <img src="../../assets/logo_sanoh_address.png" alt="Sanoh Logo">
                        </div>
                        <div class="company-info">
                            <h3>Purchase Order</h3>
                        </div>
                        <div class="page-info">
                            <span class="page-number"></span>
                        </div>
                    </div>
                    <div class="details">
                        <div class="details-left">
                            <div><strong>To</strong> <span>: <strong>{{ $data['supplier_name'] }}</strong></span></div>
                            <div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>{{ $data['supplier_code'] }}</strong></span></div>
                            <div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>{{ $data['supplier_address'] }}</strong></span></div>
                            <br>
                            <div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Phone Number: {{ $data['phone_number'] }}</span></div>
                            <div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax Number: {{ $data['fax_number'] }}</span></div>
                            <br>
                            <div><strong>Attn</strong> <span>: {{ $data['attn'] }}</span></div>
                            <br>
                            <div><span>Please supply the following</span></div>
                            <br>
                        </div>

                        <div class="details-right">
                            <div class="row">
                                <div class="label">P/O NO</div>
                                <div class="value">: {{ $data['po_number'] }}</div>
                            </div>
                            <div class="row">
                                <div class="label">Date</div>
                                <div class="value">: {{ $data['po_date'] }}</div>
                            </div>
                            <div class="row">
                                <div class="label">P/O Type</div>
                                <div class="value">: {{ $data['po_type'] }}</div>
                            </div>
                            <div class="row">
                                <div class="label">PR</div>
                                <div class="value">: {{ $data['pr_no'] }}</div>
                            </div>
                            <div class="row">
                                <div class="label">Planned Receipt</div>
                                <div class="value">: {{ $data['planned_receipt_date'] }}</div>
                            </div>
                            <div class="row">
                                <div class="label">Delivery Terms</div>
                                <div class="value">: {{ $data['delivery_term'] }}</div>
                            </div>
                            <div class="row">
                                <div class="label">Currency</div>
                                <div class="value">: {{ $data['currency'] }}</div>
                            </div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Seq</th>
                                <th>Description <br>Part No.</th>
                                <th>Delivery Date</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;
        }

        // Add item rows
        pageContent += `
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['seq_no'] }}</td>
                <td>{{ $item['part_name'] }} <br> {{ $item['part_number'] }}</td>
                <td>{{ $item['delivery_date'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ $item['unit'] }}</td>
                <td>{{ $item['unit_price'] }}</td>
                <td>{{ $item['amount'] }}</td>
            </tr>`;
        subtotal = data.total_amount;
    });

    // Add subtotal, PPN, and total
    if (items.length > 0) {
        pageContent += `
        <tr>
            <th colspan="6"></th>
            <th style="text-align: right;">Subtotal</th>
            <th style="text-align: right;">{{ $subtotal }}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
            <th style="text-align: right;">PPN 11%</th>
            <th style="text-align: right;">{{ $subtotal * 0.11}}.toFixed(2)}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
            <th style="text-align: right;">Total</th>
            <th style="text-align: right;">{{ $subtotal * 1.11}}.toFixed(2)}</th>
        </tr>

            </tbody>
            </table>
            <div class="terms" style="margin-top: 20px;">
            <div style="display: flex; margin-bottom: 5px;">
                <div style="min-width: 100px; font-weight: bold;">Note</div>
                <div style="min-width: 10px;">:</div>
                <div>{{ $data['note'] }}</div>
            </div>
            <div style="display: flex; margin-bottom: 5px;">
                <div style="min-width: 100px; font-weight: bold;">Delivery</div>
                <div style="min-width: 10px;">:</div>
            </div>
            <div style="display: flex; margin-bottom: 5px;">
                <div style="min-width: 100px; font-weight: bold;">Terms</div>
                <div style="min-width: 10px;">:</div>
                <div>{{ $data['terms'] }}</div>
            </div>
            <div style="display: flex;">
                <div style="min-width: 100px; font-weight: bold;">Delivery Place</div>
                <div style="min-width: 10px;">:</div>
                <div>
                    PT. SANOH INDONESIA <br>
                    JL. INTI II BLOK C4 NO. 10 KAWASAN INDUSTRI HYUNDAI, <br>
                    LEMAH ABANG - BEKASI 17750 <br>
                    <div>{{ $data['phone_number'] }} {{ $data['fax_number'] }}</div>
                </div>
            </div>
        </div>
        <!-- Signature Section -->
    <div style="display: flex; justify-content: space-between; margin-top: 150px;">
        <div style="text-align: left; font-size: 12px;">
            <p>Accepted and Confirmed</p>
            <p>{{ $data['supplier_name'] }}</p>
        </div>

        <table style="border-collapse: collapse; text-align: center; width: 60%;">
            <thead>
                <tr>
                    <th style="border: 1px solid rgb(222, 222, 222); width: 33%;">Prepared</th>
                    <th style="border: 1px solid rgb(222, 222, 222); width: 33%;">Checked</th>
                    <th style="border: 1px solid rgb(222, 222, 222); width: 33%;">Approved</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid rgb(222, 222, 222); height: 100px; width: 22%;"></td>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 22%;"></td>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 22%;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 33%; text-align: center;"></td>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 33%; text-align: center;">FADLI YUSRAL</td>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 33%; text-align: center;">MISBAHUL MUNIR</td>
                </tr>
                <tr>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 33%; text-align: center;">Purchasing</td>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 33%; text-align: center;">Dept. Manager</td>
                    <td style="border: 1px solid rgb(222, 222, 222); width: 33%; text-align: center;">President Director</td>
                </tr>
            </tbody>
        </table>
        </div>`;
    }

    return pageContent;
}

</script>

</body>

</html>
