<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
            @foreach ($users as $i)
            <tr>
                <td>{{$i -> bp_code}}</td>
                <td>{{$i -> bp_name}}</td>
            </tr>
            @endforeach
    </table>
</body>
</html>
