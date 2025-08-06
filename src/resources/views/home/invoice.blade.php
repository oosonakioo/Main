<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>KIS Invoice</title>
    <style>
      body {
        font-family: 'examplefont';
        font-size: 18px;
        line-height: 0.8;
      }
      table {
        border-spacing: 0px;
      }
      h2 {
        font-size: 29px;
      }
      .borderl {
        border-left: 1px solid;
      }
      .borderr {
        border-right: 1px solid;
      }
      .bordert {
        border-top: 1px solid;
      }
      .borderb {
        border-bottom: 1px solid;
      }
      .padding4{
        padding: 4px;
      }
      .padding4notop{
        padding: 0px 4px 4px 4px;
      }
      .padding7{
        padding: 7px;
      }
      .padding10{
        padding: 10px;
      }
      .alignt{
        vertical-align: top;
        padding-left: 4px;
      }
      .alignb{
        vertical-align: bottom;
      }
      .alignc{
        text-align: center;
      }
      .alignr{
        text-align: right;
      }
      .alignl{
        text-align: left;
      }
      .fontextrasmall{
        font-size: xx-small;
      }
      .fontsmall{
        font-size: small;
      }
      .fontlarge{
        font-size: large;
      }
      .fontbold{
        font-weight: bold;
      }
      .fonts10{
        font-size: 10px;
      }
      .fonts16{
        font-size: 16px;
      }
      .add15{
        line-height: 2.0cm;
      }
      .add1{
        line-height: 0.4cm;
      }

      .lineheight06 {
        line-height: 0.6em;
      }
      .lineheight05 {
        line-height: 0.5em;
      }
      .lineheight04 {
        line-height: 0.4em;
      }
      .lineheight10 {
        line-height: 1.0em;
      }
      .lineheight08 {
        line-height: 0.8em;
      }
    </style>
</head>
<body>

  {{--*/
    $remove_border = "";
  /*--}}

  @if ($barcode_gen != "" || $qrcode_gen != "")
    {{--*/
      $remove_border = "borderb";
    /*--}}
  @endif

  <table width="100%">
    <tr>
      <td width="50%" class="padding4">
      </td>
      <td width="50%" class="padding7 alignr fonts16">
        สำหรับลูกค้า / For Customer
      </td>
    </tr>
  </table>

  <div class="add1">&nbsp;</div>

  <h2 style="margin-bottom: 0px;">KIS International School</h2>
  <table width="100%">
    <tr>
      <td class="lineheight04">
        Parich-Pichaya Co.,Ltd.
      </td>
    </tr>
    <tr>
      <td class="lineheight04">
        999/124 Pracha-Uthit Road, Samsennok, Huai Khwang, Bangkok 10310
      </td>
    </tr>
    <tr>
      <td class="lineheight04">
        Tel. +66 (0) 2274 3451
      </td>
    </tr>
  </table>

  <table width="100%">
    <tr>
      <td width="30%" class="alignb">{{ $contfax }}</td>
      <td width="40%" class="alignc"><h2 style="text-align: center;margin: 5px 0px 0px 0px;line-height:0.6;">INVOICE</h2></td>
      <td width="30%">&nbsp;</td>
    </tr>
  </table>

  <div style="width:100%;height: 88px;">
    <div style="border: 1px solid;border-radius: 7px;width: 54%; height: 88px;float:left;">
      <table width="100%">
        <tr>
          <td width="25%" class="padding4 alignt lineheight10">
            <strong>To</strong>
          </td>
          <td width="75%" class="padding4 alignt lineheight10">
            {{ $contactname }}
            <br>
            @if (preg_match('/\p{Thai}/u', $custadd) === 1)
                <span style="font-family: 'sarabanfont';font-size: small;">{{ $custadd }}</span>
            @else
                {{ $custadd }}
            @endif
          </td>
        </tr>
        <tr>
          <td class="padding4notop lineheight10">
            <strong>Student Name</strong>
          </td>
          <td class="padding4notop lineheight10">
            {{ $custcode }}
          </td>
        </tr>
      </table>
    </div>
    <div style="border: 1px solid;border-radius: 7px;width: 45%; height: 88px; float:right;">
      <table width="100%">
        <tr>
          <td width="20%" class="padding4 alignl lineheight06">
            <strong>Invoice#</strong>
          </td>
          <td width="30%" class="padding4 alignl lineheight06">
            {{ $docuno }}
          </td>
          <td width="20%" class="padding4 alignl lineheight06">
            <strong>Date</strong>
          </td>
          <td width="30%" class="padding4 alignl lineheight06">
            {{ $docudate }}
          </td>
        </tr>
        <tr>
          <td class="padding4 borderb alignl lineheight06">
            <strong>Reference#</strong>
          </td>
          <td colspan="3" class="padding4 borderb alignl lineheight06">

          </td>
        </tr>
        <tr>
          <td colspan="2" class="padding4 alignl lineheight06">
            <strong>Payment Due Date</strong>
          </td>
          <td colspan="2" class="padding4 alignl lineheight06">
            <span style="font-size:larger">{{ $shipdate }}</span>
          </td>
        </tr>
        <tr style="line-height: 0.6;">
          <td colspan="4" class="padding4 alignl fontsmall lineheight06">
            {{ $penalty }}
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div style="border: 1px solid;border-top-left-radius: 7px; border-top-right-radius: 7px;width: 100%; margin-top: 0px;">
    <table width="100%" style="border-bottom: 1px solid;border-radius:7px;">
      <tr>
        <td width="15%" class="alignc padding4 borderr lineheight06">
          <strong>Code</strong>
        </td>
        <td width="73%" colspan="2" class="alignc padding4 borderr lineheight06">
          <strong>Items</strong>
        </td>
        <td width="12%" class="alignc padding4 lineheight06">
          <strong>Amount (THB)</strong>
        </td>
      </tr>
    </table>
    <table width="100%">
      {{--*/
        $item_loop = 0;
        $forcesmall = "";
        $forcestyle = "";

        if (strlen($text_total) > 80) {
          $forcesmall = " fontextrasmall";
        } else {
          $forcesmall = " fontsmall";
        }
      /*--}}

      @foreach($paymentdetail as $obj)
        {{--*/
          $item_loop++;
        /*--}}
        <tr>
          <td width="15%" class="borderr lineheight06" style="padding-left:4px;">
            {{ $obj->goodcode }}
          </td>
          <td width="73%" colspan="2" class="borderr lineheight06" style="padding-left:4px;">
            {{ $obj->goodnameeng1 }}
          </td>
          <td width="12%" class="alignr lineheight06" style="padding-right:4px;">
            {{ $english_format_number = number_format($obj->rematotalamnt, 2) }}
          </td>
        </tr>
      @endforeach
      <tr>
        <td colspan="2" class="padding4 alignc borderb bordert borderr lineheight06{{ $forcesmall }}">
          {{ $text_total }}
        </td>
        <td width="12%" class="padding4 alignr borderb bordert borderr lineheight06">
          <strong>Total Amount</strong>
        </td>
        <td class="padding4 alignr borderb bordert lineheight06">
          {{ $english_format_number = number_format($grandtotal, 2) }}
        </td>
      </tr>
      <tr>
        <td colspan="2" class="padding4 alignt borderr lineheight06">
          <strong>Remark</strong>
          <br />{{ $remark }}
        </td>
        <td colspan="2" style="height:50px;" class="padding4 alignb alignc lineheight06">
          Finance Officer
        </td>
      </tr>
    </table>
  </div>

  <table width="100%" class="borderb">
    <tr>
      <td class="lineheight06">
        <span style="text-decoration: underline;">Methods of Payment</span>
      </td>
    </tr>
    <tr>
      <td class="padding4 lineheight05" style="padding-top:0px;font-size: 16px;">
        {!! $method !!}
      </td>
    </tr>
  </table>

  @if ($barcode_gen == "" && $qrcode_gen == "")
  @else
    <!--<div class="add15">&nbsp;</div>-->
  @endif

  <table width="100%">
    <tr>
      <td width="50%" class="padding4">
      </td>
      <td width="50%" class="padding4 alignr fonts16">
        สำหรับธนาคาร / For Bank
      </td>
    </tr>
  </table>

  <div class="add15">&nbsp;</div>

  <table width="100%" style="border: 1px solid;">
    <tr>
      <td colspan="4" width="65%" class="borderb borderr padding4 alignt">
        <table width="100%" padding="0" class="alignt">
          <tr>
            <td width="100%">KIS International School</td>
          </tr>
          <tr>
            <td>เ<span style="font-family: 'sarabanfont';">พื่</span>อเข้าบัญชี บจก.พริษฐ์-พิชญาเ<span style="font-family: 'sarabanfont';">พื่</span>อร.ร.นานาชาติเคไอเอส</td>
          </tr>
          <tr>
            <td class="lineheight08">
              @if($bank01 != '')
                <span><input type="checkbox" name="bank-scb" />&nbsp;&nbsp;<img src="{{ asset('images/logo-scb.jpg') }}" width="16px" style="vertical-align:middle;">&nbsp;&nbsp;{{ $bank01 }}</span><br />
              @endif
              @if($bank02 != '')
                <span><input type="checkbox" name="bank-bbl" />&nbsp;&nbsp;<img src="{{ asset('images/logo-bbl.jpg') }}" width="16px" style="vertical-align:middle;">&nbsp;&nbsp;{{ $bank02 }}</span><br>
              @endif
              @if($bank03 != '')
                <span><input type="checkbox" name="bank-kbank" />&nbsp;&nbsp;<img src="{{ asset('images/logo-kbank.jpg') }}" width="16px" style="vertical-align:middle;">&nbsp;&nbsp;{{ $bank03 }}</span>
              @endif
            </td>
          </tr>
        </table>
      </td>
      <td colspan="2" width="35%" class="borderb padding4 lineheight06 alignt">
        <table width="100%" padding="0">
          <tr>
            <td class="lineheight06 alignr" width="100%">Bill Payment Form</td>
          </tr>
          <tr>
            <td class="lineheight06"><span>Branch ________________</span><span>&nbsp;Date _______________</span></td>
          </tr>
          <tr>
            <td class="lineheight06"><span>Name: {{ $studentname }}</span></td>
          </tr>
          <tr>
            <td class="lineheight06"><span>REF#1 (ID): {{ $custcode_ }}</span></td>
          </tr>
          <tr>
            <td class="lineheight06"><span>REF#2 : {{ $docuno }}</span></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td nowrap width="10%" rowspan="2" class="borderb borderr padding4 lineheight06">
        <span><input type="checkbox" name="pay-cash" />&nbsp;Cash</span><br />
        <span><input type="checkbox" name="pay-cheque" />&nbsp;Cheque</span>
      </td>
      <td width="15%" class="borderb borderr padding4 alignc lineheight06">
        Cheque No
      </td>
      <td width="20%" class="borderb borderr padding4 alignc lineheight06">
        Bank / Branch
      </td>
      <td nowrap width="20%" class="borderb borderr padding4 alignc lineheight06">
        Amount (THB)
      </td>
      <td width="17%" class="alignt alignc borderr {{ $remove_border }} lineheight06" rowspan="3">
        <span style="font-size: x-small;">Deposit by / Tel.</span>
      </td>
      <td width="18%" class="alignt alignc {{ $remove_border }} lineheight06" rowspan="3">
        <span style="font-size: x-small;">สำหรับเจ้าหน้า<span style="font-family: 'sarabanfont';font-size: small;">ที่</span>ธนาคาร</span>
      </td>
    </tr>
    <tr>
      <td class="borderb borderr padding4 lineheight04">
      </td>
      <td class="borderb borderr padding4 lineheight04">
      </td>
      <td class="borderb borderr padding4 alignr lineheight04">
        {{ $english_format_number = number_format($grandtotal, 2) }}
      </td>
    </tr>
    <tr>
      <td colspan="4" class="borderr alignc {{ $remove_border }}{{ $forcesmall }}">
        {{ $text_total }}
      </td>
    </tr>

    @if ($barcode_gen != "" || $qrcode_gen != "")
      <tr>
        <td colspan="3" class="padding10 alignt" width="60%">
            @if ($barcode_gen != "")
              <?php
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode_gen, $generator::TYPE_CODE_128)) . '" width="302px" height="32px" style="padding: 20px 0px 0px 30px;">';
              ?>
              <div class="fonts10" style="margin-top: -10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{!! $displaybarcodestr !!}</div>
            @endif
        </td>
        <td colspan="3" width="40%" class="alignr alignt">
          @if ($qrcode_gen != "")
            <img id='qrcode' src="https://api.qrserver.com/v1/create-qr-code/?data={{ $qrcode_gen }}&amp;size=78x78"/>
          @else
            <table>
              <tr>
                <td height="100px">&nbsp;</td>
              </tr>
            </table>
          @endif
        </td>
      </tr>
    @endif
  </table>
</body>
</html>
7