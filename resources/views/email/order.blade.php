<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email order</title>
</head>

<body>
    Thank you for your order
    @php
        $orderInfor = getOrderInfor();
    @endphp

    {{$orderInfor->id}}
</body>

</html>
