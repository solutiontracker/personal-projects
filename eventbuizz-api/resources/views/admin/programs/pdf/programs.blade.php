<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet" />
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        h1 {
            margin-bottom: 25px;
        }

        h2 {
            margin-bottom: 12px;
            color: #888;
        }

        h4,
        p {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .invoice-box {
            max-width: 850px;
            margin: auto;
            padding: 30px 0;
            font-size: 14px;
            line-height: 1.4;
            font-family: 'Open Sans', sans-serif;
            color: #000;
            position: relative;
            text-align: left;
        }

        table tr td,
        table tr th {
            vertical-align: top;
            font-weight: 400;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        @if(count($programs_array) > 0)
        <table style="width: 100%" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <th>
                        <h1>Program sessions</h1>
                    </th>
                </tr>

                @foreach($programs_array as $date => $programs)
                <tr>
                    <td style="padding-left: 20px;border-left: 5px solid #F38330">

                        <table style="width: 100%;" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <h2>{{ $programs[0]['heading_date'] }}</h2>
                                    </td>
                                </tr>


                                @foreach($programs as $program)
                                <tr>
                                    <td style="padding-bottom: 10px;" colspan="2">
                                        <table style="width: 100%" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    @if(count($program['program_speakers']) > 0)
                                                    <td style="width: 80%">Speakers: {{implode(', ', $program['program_speakers'] ?? [])}}</td>
                                                    @endif
                                                    <td style="text-align: right; width: 20%;font-weight: bold;font-size: 16px;">{{ $program['location'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 12%; font-weight: bold;">{{ \Carbon\Carbon::parse($program['start_time'])->format('H:i') }}</td>
                                    <td style="width: 88%;">
                                        <h4>{{$program['topic']}}</h4>
                                        <p>{!! $program['description'] !!}</p>
                                        @if(count($program['program_tracks']) > 0)
                                        <p>{{ implode(', ', $program['program_tracks'] ?? []) }}</p>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px">&nbsp;</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</body>

</html>