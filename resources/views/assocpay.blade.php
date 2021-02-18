<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        
        <title>Management Fee</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}
            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        Sir/Madam,
      <p> Payment has to be send to Mr./M/s. {{$Assoc->Assoc_FirstName}} {{$Assoc->Assoc_LastName}}.
      Transaction details updated by {{$User}}.</p>
      <table class="table">
        <thead>
            <tr>
            <th>Work ID</th>
            <th>Associate</th>
            <th>Associate Payable</th>
            <th>Transaction ID</th>
            <th>Transaction Date</th>
            <th>Transaction Type</th>
            </tr>
        </thead>
      
      <tbody>
        <tr>
            <td>{{$workID}}</td>
            <td>{{$Assoc->Assoc_FirstName}} {{$Assoc->Assoc_LastName}}</td>
            <td>Rs. {{$APay}}</td>
            <td>{{$TransID}}</td>
            <td>{{$A_PaidDate}}</td>
            <td>{{$Trans_Type}}</td>
      </tr>
      </tbody>
      </table>
    </body>
</html>
