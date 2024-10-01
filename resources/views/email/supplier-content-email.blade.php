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
    <h2>PO RESPONSE TES</h2>

    {{-- Content --}}
    {{-- @dd($data) --}}
    @if (isset($data) && count($data) > 0)
        @foreach ($data as $po)
                <p>{{ $po['po_no'] }}</p>
        @endforeach
    @else
        <p>kosong</p>
    @endif



    {{-- Footer --}}
    <footer>Footer</footer>
</body>
</html>
