<html>
<head>
    <title>{{ $title }} {{ $issue }}</title>
    <style type="text/css" charset=utf-8>
        <!--
        .MsoNormal {
            margin-top: 0in;
            margin-right: 0in;
            margin-bottom: 0in;
            margin-left: 0in;
            font-size: 13px;
            color: #424242;
            font-family: Tahoma, Thonburi, Arial, Helvetica;
        }

        @media screen and (max-width: 639px) {
            table[class="wrapper"] {
                width: 100% !important;
            }
        }

        .wrapper, .wrapHead, .wrapFooter {
            width: 980px;
            display: block;
            position: relative;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            margin: 0 auto;
            padding: 0 2px;
        }

        .size {
            width: 250px;
            padding-top: 1px;
        }

        -->
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" class='MsoNormal'>
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
    <tr>
        <td align="left">
            <table class="wrapper" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF"
                   style="max-width: 640px">
                <tfoot>
                <tr>
                    <td colspan="2" height="100"></td>
                </tr>
                </tfoot>
                <tbody>
                <tr>
                    <td valign="top" colspan="2">
                        <table width="100%" border="0" cellspacing="0" cellpadding="8" bgcolor="#FFFFFF"
                               class='MsoNormal'>
                            <tr>
                                <td colspan="2">เรียน ผู้ดูแลเรื่อง {{ $title }}
                            </tr>

                            <tr>
                                <td colspan="2"><br>หัวข้อการติดต่อ: {{ $title }} {{ $issue }}<br>
                                    รุ่นรถ: {{ $type }}<br>
                                    ชื่อ-นามสกุล: {{ $name }}<br>
                                    เบอร์โทรศัพท์: {{ $tel }}<br>
                                    อีเมล์: {{ $email }}<br>
                                    รายละเอียด: {!! $detail !!}<br>
                            <tr>
                                <td colspan="2">
                                    <div style="color:#999; font-size:12px;">*** อีเมลนี้เป็นการแจ้งจากระบบอัตโนมัติ
                                        กรุณาอย่าตอบกลับ ***
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>