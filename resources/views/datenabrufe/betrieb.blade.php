<html>
    <head>
        <style>
            .table-blue {
            margin: Auto;
            background-color: #DFEEFF;
            }
            
            .table-blue tr th,
            .table-blue tr td {
            border: Outset 1px #7DBBFF;
            }
            
            .table-blue tr th,
            .table-blue tr td {
            padding: 10px;
            }
        </style>
    </head>
    <body>
        @include('intranet-app-formwerk::datenabrufe.betrieb-gewerke')
        @include('intranet-app-formwerk::datenabrufe.betrieb-unternehmen')
        @include('intranet-app-formwerk::datenabrufe.betrieb-personen')
    </body>
</html>
