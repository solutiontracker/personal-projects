<html>
<head>
    <meta charset="utf-8">
    <title>{{$eventSetting['name']}}-Sub Registration</title>
    <style>
        .invoiceBox {
            max-width: 900px;
            margin: auto;
            padding: 0px 0;
            font-size: 15px;
            line-height: 1.4;
            font-family: 'Open Sans', sans-serif;
            color: #636466;
        }


        .divTable {
            display: table;
            color: #636466;
            width: 100%;
            font-family: Open Sans, sans-serif;
            font-size: 14px;
        }

        .divTableRow {
            display: table-row;
            border: none;
        }

        .divTableHeading {
            font-size: 13px;
            font-weight: 700;
            background: transparent !important;
            display: table-header-group;
        }

        .divTableCell,
        .divTableHead {
            border-top: 1px solid #bebebe;
            display: table-cell;
            padding: 12px;
            text-align: right;
            width: 13%;
        }

        .divTableBody {
            display: table-row-group;
        }

        .divBlock {
            display: inline-table;
            margin: 0 auto;
            width: 100%;
        }
        .divBlock .divTable:last-of-type {
            border-bottom: none !important;
        }
    </style>
</head>
<body>
<div class="invoiceBox">
    <div class="divBlock">
        <div class="divTable">
            <div class="divTableBody">
                <div class="divTableRow">
                    <div class="divTableHead divTableHeading"
                         style="border-top: none;text-align: center;font-size: 27px; color: #101010;font-weight: 600;padding-bottom: 25px;padding-top: 25px;">
                        {{ $labels['EVENTSITE_QUESTIONAIRS_MAIN'] }}
                    </div>
                </div>
            </div>
        </div>
        @foreach ($sub_registration->question as $i => $question)
            @php $ans_value = ''; @endphp
            @if(count($question['result']) > 0)
                @if($question['question_type'] == 'single' || $question['question_type'] == 'multiple' || $question['question_type'] == 'dropdown')
                     @php $temp_ans = array(); @endphp
                         @foreach ($question['result'] as $ans_id)
                            @php $temp_ans[] = $ans_id['answer_id']; @endphp
                         @endforeach
                     @php $ans_value = ""; @endphp
                     @foreach ($question['answer'] as $answer)
                         @if(in_array($answer['id'], $temp_ans))
                             @php $ans_value .= ' ' . $answer['info'][0]['value'] . ','; @endphp
                         @endif
                     @endforeach
                @else
                    @php $ans_value = $question['result'][0]['answer']; @endphp
                @endif
                <div class="divTable" style="padding-bottom: 15px; border-bottom: 1px solid #bebebe;margin-bottom: 15px;">
                    <div class="divTableBody">
                        <div class="divTableRow">
                            <div class="divTableHead divTableHeading"
                                 style="border-top: none; text-align: left;font-size: 20px;font-weight: 600;color: #000;padding: 3px;padding-left: 0;">
                                {{ $question['info'][0]['value'] }}
                            </div>
                        </div>
                        <div class="divTableRow">
                            <div class="divTableCell"
                                 style="padding: 3px;text-align: left; padding-left: 0;font-size: 18px; color: #707070;border: none;">
                                {{ $ans_value }}
                            </div>
                        </div>
                        @if($question['enable_comments'] == 1)
                            <div class="divTableRow">
                                <div class="divTableCell" style="padding: 3px;text-align: left; padding-left: 0;font-size: 18px; color: #707070;border: none;">
                                    {{$question['result'][0]['comments']}}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
</body>
</html>