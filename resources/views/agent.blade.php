<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agent's Parser</title>
    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
</head>
<body>
    <div class="container">
        <h1>Hi guys. You can find some usefull things</h1>
        <div class="checkSale">
            <form action="{{ route('checkSale') }}" methon="POST">
{{--                @csrf--}}
                <div class="form-group">
                    <label for="sessionId">Session ID</label>
                    <input type="text" name="sessionId" id="sessionId">
                </div>
                @csrf
                <button type="submit">Submit</button>
            </form>
            <br>
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <td>Ticket Status</td>
                            <td>Count</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Prodannay</td>
                            <td>123</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
