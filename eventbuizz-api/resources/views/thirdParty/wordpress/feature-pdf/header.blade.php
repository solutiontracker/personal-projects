<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF EventBuizz</title>
    <style>
        * {
            padding: 0;
            margin: 0;
        }
       
        body {
            font-family: 'SF Pro Display', sans-serif;
            font-size: 14px;
            color: #fff;
            background: #354F5B;
            line-height: 1.5;
        }
        .ebs-image-box {
            min-width: 72px;
            height: 72px;
            line-height: 72px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 100%;
            background: linear-gradient(-40deg, #2b3d47 0%,#acb8c1 100%);
            background: -webkit-linear-gradient(-40deg, #2b3d47 0%,#acb8c1 100%);
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2), 1px 1px 0 1px rgba(255,255,255,0.3) inset;
        }
        .ebs-image-box img {
            width: auto;
            height: auto;
            max-width: 60%;
            max-height: 60%;
            display: inline-block;
            vertical-align: middle
        }
        #wrapper {
            width: 100%;
            margin: auto;
        }
        @media print {
            #wrapper {
              max-width: 100%;
            }
        }

        @media print {body {-webkit-print-color-adjust: exact;}

    }
        
    </style>
</head>
<body>
    <div id="wrapper">
        <table style="background: #F3F1F0; border-bottom: 12px solid #FFD161; color: #45616E;font-family: 'Sango Rounded', sans-serif; font-weight: 300;" width="100%">
            <tr>
                <td style="padding: 30px" valign="middle">
                    <img src="https://www.eventbuizz.com/wp-content/uploads/2023/10/EventBuizz.svg" alt="">
                </td>
                <td style="text-align: right; padding-right: 30px;" valign="middle">
                    <div style="text-align: left; display: inline-block;">
                        <strong>Email:</strong> info@eventbuizz.com <br>
                        <strong>Website:</strong> www.eb.eventbuizz.com
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>