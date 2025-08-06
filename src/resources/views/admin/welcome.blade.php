@extends('layouts.admin')

@section('header', trans('admin.welcome'). ' : '. studly_case(Auth::user()->name))

@section('content')

    <br>
    <div id="clockbox" style="color:green;"></div>
    {{ trans('admin.welcome-2') }}

    <script type="text/javascript">
      <?php if ($lang == 'th'){ ?>
        tday = new Array("วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่");
      <?php }else{ ?>
        tday = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
      <?php } ?>

      <?php if ($lang == 'th'){ ?>
        tmonth = new Array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
      <?php }else{ ?>
        tmonth = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
      <?php } ?>

      function GetClock(){
        var d = new Date();
        var nday = d.getDay(), nmonth = d.getMonth(), ndate = d.getDate(), nyear = d.getYear();
        if(nyear<1000) nyear+=1900;
        var nhour = d.getHours(),nmin=d.getMinutes(),nsec=d.getSeconds(),ap;

        if(nhour==0){ap=" AM";nhour=12;}
        else if(nhour<12){ap=" AM";}
        else if(nhour==12){ap=" PM";}
        else if(nhour>12){ap=" PM";nhour-=12;}

        if(nmin<=9) nmin="0"+nmin;
        if(nsec<=9) nsec="0"+nsec;

        <?php if ($lang == 'th'){ ?>
          document.getElementById('clockbox').innerHTML=""+tday[nday]+" "+ndate+" "+tmonth[nmonth]+" "+nyear+" "+nhour+":"+nmin+":"+nsec+ap+"";
        <?php }else{ ?>
          document.getElementById('clockbox').innerHTML=""+tday[nday]+", "+tmonth[nmonth]+" "+ndate+", "+nyear+" "+nhour+":"+nmin+":"+nsec+ap+"";
        <?php } ?>
      }

      window.onload=function(){
        GetClock();
        setInterval(GetClock,1000);
      }
    </script>

@endsection
