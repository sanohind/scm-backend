<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    {{-- header --}}
    <h2>PO RESPONSE</h2>

    {{-- Content --}}
    <p>Supplier_code = {{ $data['supplier_code'] }}</p>
    <p>Po_code = {{ $data['po_no'] }}</p>
    <p>Status po = {{ $data['response'] }}</p>
    <p>Reason = {{ $data['response'] }}</p> {{-- column for reason--}}

    {{-- Footer --}}
    <footer>Footer</footer>
</body>
</html>
