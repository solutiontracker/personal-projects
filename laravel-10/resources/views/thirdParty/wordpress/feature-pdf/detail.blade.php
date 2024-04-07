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
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }

        body {
            font-family: 'SF Pro Display', sans-serif;
            font-size: 14px;
            color: #fff;
            line-height: 1.5;
        }

        strong {
            letter-spacing: 1px;
        }

        .ebs-image-box {
            min-width: 72px;
            height: 72px;
            line-height: 68px;
            float: left;
            margin-right: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 100%;
            background: linear-gradient(-40deg, #2b3d47 0%, #acb8c1 100%);
            background: -webkit-linear-gradient(-40deg, #acb8c1 0%, #2b3d47 100%);
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2), 1px 1px 0 1px rgba(255, 255, 255, 0.3) inset;
        }

        .ebs-image-box img {
            width: auto;
            height: auto;
            max-width: 60%;
            max-height: 60%;
            display: inline-block;
            vertical-align: middle
        }

        .canvas {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100000mm;
            z-index: 1;
            background: #354F5B;

        }

        #wrapper {
            width: 100%;
            margin: auto;
            height: 100%;
            position: relative;
            z-index: 8;
            background: #354F5B;


        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="canvas"></div>
    <div id="wrapper">

        <table border="0" cellpadding="0" cellspacing="0" style="line-height: 0" width="100%">
            <thead style="border: none;">
                <tr>
                    <th valign="top" style="background: #354F5B;" colspan="2">
                        <table width="100%" border="0"
                            style="border-bottom: 12px solid #FFD161;color: #45616E;font-family: 'Sango Rounded', sans-serif; font-weight: 300; background:#F3F1F0"
                            width="100%">
                            <tr>
                                <td style="padding: 30px; text-align: left;" valign="middle">
                                    <img src="https://www.eventbuizz.com/wp-content/uploads/2023/10/EventBuizz.svg"
                                        alt="">
                                </td>
                                <td style="text-align: right; padding-right: 30px;" valign="middle">
                                    <div
                                        style="text-align: right; display: inline-block;line-height: 1.5;font-family: 'SF Pro Display', sans-serif; font-weight: 400">
                                        sales@eventbuizz.com <br>
                                        www.eventbuizz.com <br>
                                        +45 6023 6666
                                    </div>
                                </td>

                            </tr>
                        </table>
                        <table width="100%" border="0" style="border: 0; background: #354F5B;" width="100%">
                            <tr>
                                <td style="padding: 10px;" colspan="2"></td>
                            </tr>
                            <tr>
                                <td style="padding: 20px; text-align: center; line-height: 1.5" colspan="2">
                                    <h3
                                        style="font-weight: 600; font-size: 36px; margin-bottom: 0px; color: #fff;font-family: 'Sango Rounded', sans-serif;">
                                        Alle de features, du har brug for</h3>
                                    <p
                                        style="text-align: center;font-family: 'SF Pro Display', sans-serif; font-weight: 400">
                                        Vores produktsuite er fuldt integreret. Ved anvendelse af flere af vores
                                        produkter, opdateres <br>
                                        og vedligeholdes data centralt. Uanset om dit event er fysisk, online eller
                                        hybrid.
                                    </p>
                                </td>
                            </tr>
                            <!-- <tr>
                                <td style="padding: 10px;" colspan="2"></td>
                            </tr> -->
                        </table>
                    </th>
                </tr>
            </thead>

            @foreach ($data as $row)
            <tr>
                <td style="padding: 0 25px; line-height: 1.5;" colspan="2" valign="top">
                    <div>
                        @foreach ($row as $item)
                        <div style="width: 50%;padding: 30px 15px 0px;float: left;">
                            <div>
                                <div class="ebs-image-box"><img width="78" height="65" src="{{$item->thumbnail_image_url}}"
                                        alt="" /></div>
                                <div style="margin-left: 92px;">{!!$item->post_content!!}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </td>
            
            </tr>
            @endforeach
        </table>
    </div>
</body>

</html>