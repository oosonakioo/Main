<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>KIS Invoice</title>
    <style>
      body {
        font-family: 'examplefont', sans-serif;
        line-height: 1.5em;
      }
      table {
        border-spacing: 0px;
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

  <h2 style="margin-bottom: 0px;">KIS International School</h2>
  <table width="100%">
    <tr>
      <td style="line-height:0.8">
        999/123-124 Pracha-Utit Road, Samsennok, Huay Kwang, Bangkok 10310
      </td>
    </tr>
    <tr>
      <td style="line-height:0.8">
        Tel. +66 (0) 2274 3451  Fax. +66 (0) 2274 3459
      </td>
    </tr>
  </table>
  <h2 style="text-align: center;margin: 5px 0px 5px 0px;">INVOICE</h2>

  <table width="100%">
    <tr>
      <td>{{ $contfax }}</td>
    </tr>
  </table>

  <div style="width:100%;height: 110px;">
    <div style="border: 1px solid;border-radius: 7px;width: 54%; height: 110px;float:left;">
      <table width="100%">
        <tr>
          <td width="25%" class="padding4 alignt">
            <strong>To</strong>
          </td>
          <td width="75%" class="padding4 alignt">
            {{ $contactname }}
            <br>{{ $custadd }}
          </td>
        </tr>
        <tr>
          <td class="padding4">
            <strong>Student Name</strong>
          </td>
          <td class="padding4">
            {{ $custcode }}
          </td>
        </tr>
      </table>
    </div>
    <div style="border: 1px solid;border-radius: 7px;width: 45%; height: 110px; float:right;">
      <table width="100%">
        <tr>
          <td width="20%" class="padding4 alignl">
            <strong>Invoice#</strong>
          </td>
          <td width="30%" class="padding4 alignl">
            {{ $docuno }}
          </td>
          <td width="20%" class="padding4 alignl">
            <strong>Date</strong>
          </td>
          <td width="30%" class="padding4 alignl">
            {{ $docudate }}
          </td>
        </tr>
        <tr>
          <td style="line-height:0.8" class="padding4 borderb alignl">
            <strong>Reference#</strong>
          </td>
          <td colspan="3" class="padding4 borderb alignl">

          </td>
        </tr>
        <tr>
          <td colspan="2" class="padding4 alignl">
            <strong>Payment Due Date</strong>
          </td>
          <td colspan="2" class="padding4 alignl">
            <span style="font-size:larger">{{ $shipdate }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="4" style="line-height:0.8" class="padding4 alignl fontsmall">
            {{ $penalty }}
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div style="border: 1px solid;border-top-left-radius: 7px; border-top-right-radius: 7px;width: 100%; margin-top: 5px;">
    <table width="100%" style="border-bottom: 1px solid;border-radius:7px;">
      <tr>
        <td width="15%" class="alignc padding4 borderr">
          <strong>Code</strong>
        </td>
        <td width="70%" colspan="2" class="alignc padding4 borderr">
          <strong>Items</strong>
        </td>
        <td width="15%" class="alignc padding4">
          <strong>Amount (THB)</strong>
        </td>
      </tr>
    </table>
    <table width="100%">
      {{--*/
        $item_loop = 0;
      /*--}}
      @foreach($paymentdetail as $obj)
        {{--*/
          $item_loop++;
        /*--}}
        <tr>
          <td width="15%" class="borderr" style="padding-left:4px;line-height: 0.8em;">
            {{ $obj->goodcode }}
          </td>
          <td width="70%" colspan="2" class="borderr" style="padding-left:4px;line-height: 0.8em;">
            {{ $obj->goodnameeng1 }}
          </td>
          <td width="15%" class="alignr" style="padding-right:4px;line-height: 0.8em;">
            {{ $english_format_number = number_format($obj->rematotalamnt, 2) }}
          </td>
        </tr>
      @endforeach
      @for ($i = 10; $i > $item_loop; $i--)
        <tr>
          <td class="borderr" style="line-height: 0.8em;">&nbsp;</td>
          <td colspan="2" class="borderr" style="line-height: 0.8em;">&nbsp;</td>
          <td style="line-height: 0.8em;">&nbsp;</td>
        </tr>
      @endfor
      <tr>
        <td colspan="2" class="padding4 alignc fontsmall borderb bordert borderr">
          {{ $text_total }}
        </td>
        <td width="15%" class="padding4 alignr borderb bordert borderr">
          <strong>Total Amount</strong>
        </td>
        <td class="padding4 alignr borderb bordert">
          {{ $english_format_number = number_format($grandtotal, 2) }}
        </td>
      </tr>
      <tr>
        <td colspan="2" class="padding4 alignt borderr">
          <strong>Remark</strong>
          <br />{{ $remark }}
        </td>
        <td colspan="2" style="height:60px;" class="padding4 alignb alignc">
          Finance Officer
        </td>
      </tr>
    </table>
  </div>

  <table width="100%" class="borderb">
    <tr>
      <td style="line-height:0.6em">
        <span style="text-decoration: underline;">Methods of Payment</span>
      </td>
    </tr>
    <tr>
      <td class="padding4 fontsmall" style="line-height:0.8em;padding-top:0px;">
        {!! $method !!}
      </td>
    </tr>
  </table>

  @if ($barcode_gen == "" && $qrcode_gen == "")
    <br>
  @endif

  <table width="100%">
    <tr>
      <td width="50%" class="padding4 fontlarge">
        KIS International School
      </td>
      <td width="50%" class="padding4 fontlarge alignr">
        Bill Payment Form
      </td>
    </tr>
  </table>

  <table width="100%" style="border: 1px solid;">
    <tr>
      <td colspan="4" width="70%" class="borderb borderr padding4">
        <span>&nbsp;สำหรับเข้าบัญชี โรงเรียนนานาชาติเคไอเอส</span><br />
        @if($bank01 != '')
          <span><input type="checkbox" name="bank-scb" />{{ $bank01 }}</span><br />
        @endif
        @if($bank02 != '')
          <span><input type="checkbox" name="bank-bkb" />{{ $bank02 }}</span><br>
        @endif
        @if($bank03 != '')
          <span><input type="checkbox" name="bank-bkb" />{{ $bank03 }}</span>
        @endif
      </td>
      <td width="30%" class="borderb padding4">
        <span>Date ___________________</span><br />
        <span>Branch _________________</span><br />
        <span>Name: {{ $studentname }}</span><br />
        <span>REF#1 (ID): {{ $custcode_ }}</span><br />
        <span>REF#2 : {{ $docuno }}</span><br /><br />
      </td>
    </tr>
    <tr>
      <td width="10%" rowspan="2" class="borderb borderr padding4">
        <span><input type="checkbox" name="pay-cash" />Cash</span><br />
        <span><input type="checkbox" name="pay-cheque" />Cheque</span>
      </td>
      <td width="15%" class="borderb borderr padding4 alignc">
        Cheque No
      </td>
      <td width="35%" class="borderb borderr padding4 alignc">
        Bank / Branch
      </td>
      <td width="20%" class="borderb borderr padding4 alignc">
        Amount (THB)
      </td>
      <td width="20%" class="alignt {{ $remove_border }}" rowspan="3">
        <span style="font-size: x-small;">Deposit by / Tel.</span>
      </td>
    </tr>
    <tr>
      <td class="borderb borderr padding4">
      </td>
      <td class="borderb borderr padding4">
      </td>
      <td class="borderb borderr padding4 alignr">
        {{ $english_format_number = number_format($grandtotal, 2) }}
      </td>
    </tr>
    <tr>
      <td colspan="4" class="borderr {{ $remove_border }} fontsmall alignc">
        {{ $text_total }}
      </td>
    </tr>

    @if ($barcode_gen != "" || $qrcode_gen != "")
      <tr>
        <td colspan="3" class="padding10" width="60%">
          &nbsp;&nbsp;&nbsp;
            @if ($barcode_gen != "")
              <?php
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode('". $barcode_gen. "', $generator::TYPE_CODE_128, 1, 40)) . '">';
              ?>
              <br>
              <span class="fonts10">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $barcode_gen }}</span>
            @endif
        </td>
        <td colspan="2" width="40%" class="alignr alignt">
          @if ($qrcode_gen != "")
            <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{ $qrcode_gen }}&chld=H|2" />
          @endif
        </td>
      </tr>
    @endif
  </table>
</body>
</html>
