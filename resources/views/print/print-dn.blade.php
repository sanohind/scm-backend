<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Delivery Note PT Sanoh Indonesia</title>
    <link rel="icon" type="image/png" href="../../assets/icon_sanoh.png">

    <style>
        body {
            font-family: 'Poppins', sans-serif; /* Mengatur jenis font */
            margin: 0;
            padding: 0;
            background-color: #f2f2f2; /* Warna latar belakang */
        }

        .container {
            width: 210mm;
            height: auto;
            max-width: 200mm;
            padding: 10px;
            background-color: #fff; /* Latar belakang putih */
            box-sizing: border-box;
            page-break-after: always; /* Pisahkan halaman setelah container */
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between; /* Atur elemen dalam header */
            align-items: center;
            margin-bottom: 20px;
            border: 1px solid #000; /* Border di sekitar header */
            padding: 8px;
        }

        .logo {
            width: 110px; /* Mengatur lebar logo */
            margin-right: 10px; /* Jarak antara logo dan konten lain */
        }

        .logo img {
            width: 100%; /* Mengatur gambar agar sesuai dengan container logo */
        }

        .company-info {
            text-align: left; /* Menyelaraskan teks ke kiri */
            flex-grow: 5; /* Memperluas agar mengisi ruang yang tersedia */
            font-size: 9px; /* Ukuran font kecil untuk info perusahaan */
        }

        .delivery-note {
            text-align: right; /* Menyelaraskan teks delivery note ke kanan */
            font-size: 11px; /* Mengatur ukuran font */
        }

        .details {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between; /* Menyelaraskan bagian details */
            font-size: 10px; /* Mengatur ukuran font untuk details */
        }

        .details-left, .details-right {
            width: 60%; /* Mengatur lebar untuk bagian details */
            margin-bottom: 1px;
            margin-left: 3px;
            margin-right: 30px; /* Jarak antar details */
            padding-right: 130px; /* Menambahkan padding di kanan */
        }

        .details-right {
            font-size: 10px;    /* Mengatur ukuran font */
            text-align: right; /* Menyelaraskan teks ke kanan */
            padding-right: 0px; /* Menambahkan padding di kanan */
        }

        .details-left {
            text-align: left; /* Mengatur teks agar rata kiri */
            font-size: 10px; /* Mengatur ukuran font menjadi 12px sesuai permintaan */
        }

        /* Styling khusus delivery-left*/
        .detail-item {
            display: flex; /* Mengatur elemen label dan nilai dalam satu baris */
            justify-content: flex-start; /* Menyelaraskan elemen di awal baris */
            line-height: 1.5; /* Memberikan jarak antar baris untuk keterbacaan */
        }

        .detail-item strong {
            min-width: 130px; /* Memberikan lebar minimum pada label agar rata dan konsisten */
        }

        .detail-item span {
            margin-left: 10px; /* Menambahkan jarak antara label (strong) dan nilai (span) */
        }

        /* Styling khusus untuk PO Number */
        .details-left .detail-item:last-child {
            font-weight: normal; /* Membuat PO Number tebal */
            font-size: 10px;    /* Mengatur ukuran font */
        }

        /* Styling khusus untuk Total Box */
        .details-right .detail-item:last-child {
            margin-top: 20px; /* Menambahkan jarak di atas elemen Total Box */
            display: flex;
            justify-content: flex-end; /* Menyelaraskan elemen ke ujung kanan */
            align-items: center; /* Menyelaraskan elemen secara vertikal di tengah */
        }

        .total-box {
            display: flex; /* Mengatur elemen secara horizontal */
            align-items: center; /* Menyelaraskan elemen di tengah secara vertikal */
        }

        .box-label {
            font-size: 10px;    /* Ukuran font untuk label "Total Box" */
            color: #000;        /* Warna hitam untuk label */
            margin-right: 2px; /* Jarak antara "Total Box" dan angka "73" */
        }

        .box-number {
            font-size: 12px;    /* Ukuran font untuk angka "73" */
            font-weight: bold;  /* Membuat angka tebal */
            color: #000;        /* Warna hitam untuk angka */
        }

        /* Styling untuk tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }

        /* Mengatur style untuk semua elemen th dan td */
        table, th, td {
            border: 1px solid #000;
            padding: 4px 8px;
            word-wrap: break-word;
        }

        /* Styling untuk elemen thead */
        thead th {
            text-align: center; /* Menyelaraskan teks di thead ke tengah */
            vertical-align: middle; /* Menyelaraskan teks secara vertikal di tengah */
            background-color: #ffffff;
            color: black;
        }

        /* Styling umum untuk elemen td di tbody */
        tbody td {
            text-align: center; /* Menyelaraskan teks di tengah secara default */
            vertical-align: middle; /* Penyelarasan vertikal di tengah */
        }

        /* Styling khusus untuk kolom Supplier Part No., Internal Part No., dan Part Name di tbody */
        tbody td:nth-child(2), /* Supplier Part No. */
        tbody td:nth-child(3)  /* Part Name */
        {
            text-align: left; /* Menyelaraskan teks ke kiri */
        }

        /* Styling untuk membuat teks "Part Name" di thead tetap di tengah */
        thead th:nth-child(3) {
            text-align: center; /* Menyelaraskan teks di kolom "Part Name" pada thead ke tengah */
        }

        th {
            background-color: #ffffff;
            color: black;
            text-align: center;
            font-size: 9px;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        /* Mengatur panjang kolom secara spesifik */
        th:nth-child(1), /* No. */
        td:nth-child(1),
        th:nth-child(4), /* Pcs/Kbn */
        td:nth-child(4),
        th:nth-child(5), /* No of Kbn */
        td:nth-child(5),
        th:nth-child(6), /* Total Qty */
        td:nth-child(6),
        th:nth-child(7), /* Confirmation Supp. */
        td:nth-child(7),
        th:nth-child(8), /* Confirmation Sanoh */
        td:nth-child(8),
        th:nth-child(9) /* Box Qty */
        td:nth-child(9) {
            width: 8%; /* Lebar yang sama untuk kolom No, Pcs/Kbn, No of Kbn, Total Qty, Confirmation Supp., Confirmation Sanoh, dan Box Qty */
        }

        th:nth-child(2), /* Supplier Part No. */
        td:nth-child(2) {
            text-align: center;
            width: 35%; /* Lebar lebih besar untuk kolom Supplier Part No. */
        }

        th:nth-child(3), /* Part Name */
        td:nth-child(3) {
            text-align: left; /* Menyelaraskan teks ke kiri untuk Part Name */
            width: 30%; /* Lebar tetap untuk kolom Part Name */
        }
        .note {
            margin-bottom: 20px; /* Jarak di bawah catatan */
            font-size: 10px; /* Mengatur ukuran font untuk catatan */
        }

        .note p {
            font-style: italic; /* Membuat teks miring */
            font-size: 11px; /* Mengatur ukuran font untuk catatan */
            line-height: 1.6; /* Jarak antar baris untuk keterbacaan */
        }

        .signature-section {
            display: flex;
            justify-content: space-between; /* Menyelaraskan bagian tanda tangan */
            margin-top: 10px; /* Jarak di atas bagian tanda tangan */
        }

        .signature-section .supplier,
        .signature-section .sanoh {
            width: 48%; /* Mengatur lebar untuk bagian tanda tangan */
        }

        .signature-section h3 {
            text-align: left; /* Menyelaraskan judul tanda tangan ke kiri */
            margin-bottom: 10px; /* Jarak di bawah judul tanda tangan */
            font-size: 10px; /* Mengatur ukuran font untuk judul tanda tangan */
            font-weight: bold; /* Membuat judul tanda tangan tebal */
        }

        .signature-section table {
            width: 100%; /* Tabel lebar penuh di bagian tanda tangan */
            border-collapse: collapse; /* Menggabungkan border tabel */
            height: 80px; /* Mengatur tinggi sel tanda tangan */
            table-layout: fixed; /* Tata letak tabel tetap */
        }

        .signature-section th,
        .signature-section td {
            border: 1px solid black; /* Border untuk sel tabel tanda tangan */
            padding: 4px; /* Padding untuk sel tanda tangan */
            text-align: left; /* Menyelaraskan teks sel tanda tangan ke kiri */
            vertical-align: top; /* Menyelaraskan teks tanda tangan ke atas */
            font-size: 10px; /* Ukuran font kecil untuk sel tanda tangan */
            height: 5px; /* Tinggi sel untuk bagian tanda tangan */
        }

        .signature-section th {
            font-weight: bold; /* Header tabel tanda tangan tebal */
            background-color: #ffffff; /* Latar belakang putih untuk header tanda tangan */
            text-align: left; /* Menyelaraskan header tanda tangan ke kiri */
            font-size: 10px; /* Ukuran font kecil untuk header tanda tangan */
            padding: 4px; /* Padding untuk header tanda tangan */
        }

        .signature-section td p {
            margin: 0;
            padding: 0;
            font-size: 10px; /* Ukuran font untuk konten tanda tangan */
        }

        .signature-section td {
            height: 50px; /* Tinggi sel untuk bagian tanda tangan */
        }

        @media print {

        .prints {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            padding-bottom: 10px;
            border-bottom: 1px solid black;
        }
        body, .container {
            margin: 0;
            padding: 0;
            box-shadow: none;
        }
        .dataTable1{
            margin-top:350px
        }
    }
        .page-break {
            page-break-before: always;
        }

        </style>

</head>

<body>

    <div class="container">
        <div id="page-content"></div>
    </div>
        <!-- Page Content Container -->

<script>
// Helper function to concatenate part descriptions
function concatenatePartName(part_name) {
    return part_name;
}

// Helper function to calculate no_of_kanban
function calculateNoOfKanban(quantity, snp) {
    return quantity / snp;
}

function renderTable(data, items) {
    let pageContent = '';
    const header = data || {};
    let rowsPerPage = 10;

    console.log('API Response:', data);

    items.forEach((item, index) => {
// Cek jika ini adalah awal dari halaman baru
if (index % rowsPerPage === 0) {
    if (index !== 0) {
        pageContent += `</tbody></table>
    <div class="note">
        <p><b>NOTE: </b> <br> 1. Untuk penggunaan PO Number pada Surat Jalan Supplier, harap mengikuti PO Number di atas<br>
        2. Saat Delivery ke Sanoh membawa form ini sebagai bukti delivery<br>
        3. Form ini juga sebagai Checksheet Receiving Supplier<br>
        4. Confirmation Supplier wajib diisi</p>
    </div>

    <div class="signature-section">
        <div class="supplier">
            <h5>SUPPLIER</h5>
            <table>
                <tr>
                    <th style="width: 33.33%; text-align: center; font-size: 10px;">LOGISTIK</th>
                    <th style="width: 33.33%; text-align: center; font-size: 10px;">CONTROLLER</th>
                    <th style="width: 33.33%; text-align: center; font-size: 10px;">DRIVER</th>
                </tr>
                <tr>
                    <th>    <br><br><br>    </th>
                    <th>    <br><br><br>    </th>
                    <th>    <br><br><br>    </th>
                </tr>
                <tr>
                    <th>Name:</th>
                    <th>Name:</th>
                    <th>Name:</th>
                </tr>
                <tr>
                    <th>Date:</th>
                    <th>Date:</th>
                    <th>Date:</th>
                </tr>
            </table>
        </div>
        <div class="sanoh">
            <h5>PT.SANOH INDONESIA</h5>
            <table>
                <tr>
                    <th style="width: 50%; text-align: center; font-size: 10px;">RECEIVER</th>
                    <th style="width: 50%; text-align: center; font-size: 10px;">CONTROLLER</th>
                </tr>
                <tr>
                    <th>    <br><br><br>    </th>
                    <th>    <br><br><br>    </th>
                </tr>
                <tr>
                    <th>Name:</th>
                    <th>Name:</th>
                </tr>
                <tr>
                    <th>Date:</th>
                    <th>Date:</th>
                </tr>
            </table>
        </div>
    </div><div class="page-break"></div>`;
    }
    pageContent += `
        <div class="header">
            <div class="logo">
                <img src="../../assets/logo-sanoh.png" alt="Sanoh Logo">
            </div>
            <div class="company-info">
                <p><b>PT. SANOH INDONESIA</b> <br>Jl. Inti II, Blok C-4 No.10, Kawasan Industri Hyundai, Cikarang, Kab. Bekasi<br>
                    Phone +62 21 89907963</p>
            </div>
            <div class="delivery-note">
                <h3>DELIVERY NOTE<br><span id="dnNumber">{{ $header['dn_number'] }}</span></h3>
            </div>
        </div>
        <div class="details">
            <div class="details-left">
                <div class="detail-item">
                    <strong>Supplier Code</strong> <span id="supplierCode">: {{ $header['supplier_code'] }}</span>
                </div>
                <div class="detail-item">
                    <strong>Supplier Name</strong> <span id="supplierName">: {{ $header['supplier_name'] }}</span>
                </div>
                <div class="detail-item">
                    <strong>DN Number</strong> <span id="dnNumberDetail">: {{ $header['dn_number'] }}</span>
                </div>
                <div class="detail-item">
                    <strong>PO Number</strong> <span id="poNumber">: {{ $header['po_number'] }}</span>
                </div>
            </div>
            <div class="details-right">
                <div class="detail-item">
                    <strong>Planned Received Date</strong> <span id="plannedReceivedDate">: {{ $header['planned_receipt_date'] }}</span>
                </div>
                <div class="detail-item">
                    <strong>Actual Received Date</strong> <span id="actualReceivedDate">: : {{ $header['planned_receipt_date || '_______________''] }}</span>
                </div>
                <div class="detail-item">
                    <div class="total-box">
                        <strong class="box-label">Total Box</strong>
                        <span id="totalBox" class="box-number">{{ $header['total_box'] }}</span>
                    </div>
                </div>
            </div>
        </div>
            <table>
                <thead>
                    <tr>
                        <th id="no-1" rowspan="2">No.</th>
                        <th id="supplier-part-no-1" colspan="1">Supplier Part No.</th>
                        <th id="part-name-1" rowspan="2">Part Name</th>
                        <th id="pcs-kbn-1" rowspan="2">Pcs/Kbn</th>
                        <th id="no-of-kbn-1" rowspan="2">No of Kbn</th>
                        <th id="total-qty-1" rowspan="2">Total Qty</th>
                        <th id="confirmation-1" colspan="2">Confirmation</th>
                        <th id="box-qty-1" rowspan="2">Box Qty</th>
                    </tr>
                    <tr>
                        <th id="internal-part-no-1">Internal Part No.</th>
                        <th id="supp-1">Supp.</th>
                        <th id="sanoh-1">Sanoh</th>
                    </tr>
                </thead>
                <tbody>`;
}

    // Menambahkan item ke tabel
    pageContent += `
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><b>{{ $item['supplier_part_number'] }}</b><br>{{ $item['internal_part_number'] }}</td>
            <td>{{ $item['part_name'] }}</td>
            <td>{{ $item['pcs_per_kamban'] }}</td>
            <td>{{ $item['no_of_kamban'] }}</td>
            <td>{{ $item['total_quantity'] }}</td>
            <td>{{ $item['confirmation_supp|| ''' ] }}</td>
            <td>{{ $item['confirmation_sanoh|| ''' ] }}</td>
            <td>{{ $item['box_quantity'] }}</td>
        </tr>
    `;
});

// Tutup tabel terakhir
if (items.length > 0) {
    pageContent += `</tbody></table><!-- Additional Pages as needed -->

        <div class="note">
            <p><b>NOTE: </b> <br> 1. Untuk penggunaan PO Number pada Surat Jalan Supplier, harap mengikuti PO Number di atas<br>
            2. Saat Delivery ke Sanoh membawa form ini sebagai bukti delivery<br>
            3. Form ini juga sebagai Checksheet Receiving Supplier<br>
            4. Confirmation Supplier wajib diisi</p>
        </div>

        <div class="signature-section">
            <div class="supplier">
                <h5>SUPPLIER</h5>
                <table>
                    <tr>
                        <th style="width: 33.33%; text-align: center; font-size: 10px;">LOGISTIK</th>
                        <th style="width: 33.33%; text-align: center; font-size: 10px;">CONTROLLER</th>
                        <th style="width: 33.33%; text-align: center; font-size: 10px;">DRIVER</th>
                    </tr>
                    <tr>
                        <th>    <br><br><br>    </th>
                        <th>    <br><br><br>    </th>
                        <th>    <br><br><br>    </th>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <th>Name:</th>
                        <th>Name:</th>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <th>Date:</th>
                        <th>Date:</th>
                    </tr>
                </table>
            </div>
            <div class="sanoh">
                <h5>PT.SANOH INDONESIA</h5>
                <table>
                    <tr>
                        <th style="width: 50%; text-align: center; font-size: 10px;">RECEIVER</th>
                        <th style="width: 50%; text-align: center; font-size: 10px;">CONTROLLER</th>
                    </tr>
                    <tr>
                        <th>    <br><br><br>    </th>
                        <th>    <br><br><br>    </th>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <th>Name:</th>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <th>Date:</th>
                    </tr>
                </table>
            </div>
        </div>`;
}

// Jika tidak ada item, tampilkan pesan kosong
if (items.length === 0) {
    pageContent = `
            <div class="header">
                <div class="logo">
                    <img src="../../assets/logo-sanoh.png" alt="Sanoh Logo">
                </div>
                <div class="company-info">
                    <p><b>PT. SANOH INDONESIA</b> <br>Jl. Inti II, Blok C-4 No.10, Kawasan Industri Hyundai, Cikarang, Kab. Bekasi<br>
                        Phone +62 21 89907963</p>
                </div>
                <div class="delivery-note">
                    <h3>DELIVERY NOTE<br><span id="dnNumber"></span></h3>
                </div>
            </div>
            <div class="details">
                <div class="details-left">
                    <div class="detail-item">
                        <strong>Supplier Code</strong> <span id="supplierCode">: {{ $header['supplier_code'] }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Supplier Name</strong> <span id="supplierName">: {{ $header['supplier_name'] }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>DN Number</strong> <span id="dnNumberDetail">: {{ $header['dn_number'] }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>PO Number</strong> <span id="poNumber">: {{ $header['po_number'] }}</span>
                    </div>
                </div>
                <div class="details-right">
                    <div class="detail-item">
                        <strong>Planned Received Date</strong> <span id="plannedReceivedDate">: {{ $header['planned_receipt_date'] }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Actual Received Date</strong> <span id="actualReceivedDate">: : {{ $header['planned_receipt_date || '_______________''] }}</span>
                    </div>
                    <div class="detail-item">
                        <div class="total-box">
                            <strong class="box-label">Total Box</strong>
                            <span id="totalBox" class="box-number">{{ $header['total_box'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
                <table>
                    <thead>
                        <tr>
                            <th id="no-1" rowspan="2">No.</th>
                            <th id="supplier-part-no-1" colspan="1">Supplier Part No.</th>
                            <th id="part-name-1" rowspan="2">Part Name</th>
                            <th id="pcs-kbn-1" rowspan="2">Pcs/Kbn</th>
                            <th id="no-of-kbn-1" rowspan="2">No of Kbn</th>
                            <th id="total-qty-1" rowspan="2">Total Qty</th>
                            <th id="confirmation-1" colspan="2">Confirmation</th>
                            <th id="box-qty-1" rowspan="2">Box Qty</th>
                        </tr>
                        <tr>
                            <th id="internal-part-no-1">Internal Part No.</th>
                            <th id="supp-1">Supp.</th>
                            <th id="sanoh-1">Sanoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8">No items found</td></tr>
                    </tbody>
                </table>`;
            }

        return pageContent;
};
</script>
</body>
</html>
