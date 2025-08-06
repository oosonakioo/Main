@extends('layouts.payment')

@section('content')
	<div id="printarea" class="container">
		<style>
			@media print /*--This is for Print--*/
			{
		    body {
		      -webkit-print-color-adjust: exact;
		    }
		    hr {
		      margin: 15px 0;
		    }
		    b {
		      font-size: 16px;
		    }
		    .box {
		      max-width: 660px;
		      width: 100%;
		      margin: 10% auto 0;
		      box-shadow: 0 0 4px 0 rgba(0,0,0,0.2);
					border: 1px solid #bbb;
		      border-radius: 10px;
		      background-color: #ffffff;
		      font-family: 'Open Sans', sans-serif;
		      color: #4d4d4d;
		      font-size: 14px;
		      position: relative;
		      line-height: 1.6;
		    }
		    .header {
		    }
		    .header .logo {
		      padding: 20px 30px;
		    }
		    .header .header-text {
		      padding: 20px 30px;
		      background-color: #0C419A !important;
		    }
		    .header .header-text img {
		      display: inline-block;
		      margin-right: 18px;
		    }
		    .header h1 {
		      font-size: 36px;
		      margin: 0;
		      color: #ffffff;
		      display: inline-block;
		    }
		    .content {
		      padding: 15px 30px 60px;
		    }
		    .content h1 {
		      color: #0C419A;
		      font-size: 36px;
		      margin: 0;
		    }
		    .btn {
		      display: none;
		    }
		    .btn-blue {
		    }
		    .btn-green {
		    }
		    .box {
		      margin-top: 0;
		    }
		    .noprint {
		      display: none;
		    }
		    .header .logo {
		      padding: 20px;
		    }
		    .header .header-text {
		      padding: 20px;
		    }
		    .header h1 {
		      width: 190px;
		        vertical-align: middle;
		        line-height: 1.2;
		    }
		    .content {
		      padding: 15px 20px 60px;
		    }
		    .content span {
		      width: 100%;
		      display: block;
		    }
		    .content .row:last-child span {
		      width: auto;
		      display: inline;
		    }
		    .btn {
		      left: 0;
		      margin: 0 auto;
		      display: block;
		    }
			}
		</style>
    <div class="box">
      <div class="header">
        <div class="logo">
					<a href="http://www.kis.ac.th/" target="_blank">
          	<img src="{{ asset('images/svg/lg_kis.svg') }}" alt="kis">
					</a>
        </div>
        <div class="header-text">
          <img src="{{ asset('images/svg/ic_success.svg') }}" alt="kis">
          <h1>Payment Confirmed</h1>
        </div>
      </div>
      <div class="content">
        <div class="row">
          <span>Thank you. Your payment has been successfully made. A receipt will be sent to your email within 2 business days.</span>
        </div>
        <hr>
					<div class="row">
						<span>Invoice :</span> <b>{{ $invoice }}</b>
					</div>
					<div class="row">
						<span>Student Name :</span> <b>{{ $studentname }}</b>
					</div>
					<div class="row">
						<span>Payment Date :</span> <b>{{ $paiddate }}</b>
					</div>
					<hr>
					<div class="row">
						<span>Amount (THB)</span>
					</div>
					<div class="row">
						<h1>{{ $english_format_number = number_format($amount, 2) }}</h1>
					</div>
					<div class="row">
						<span>{{ $amounttext }}</span>
					</div>
        <hr>
				<div class="row">
          <span>Remark :</span><strong> {!! $remark !!} </strong>
					<strong><br>Credit Card Fee {{ $percent }}% is {{ $english_format_number = number_format($priceadd, 2) }} Baht
					<br>Total amount including Credit Card Fee is {{ $english_format_number = number_format($totalwithfee, 2) }} Baht
					</strong>
					<div class="noprint">
						<button type="button" class="btn btn-blue" style="left: 0px;" onclick="window.print();">Print</button>
					</div>
        </div>
      </div>
			<div class="noprint">
      	<button type="button" class="btn btn-green" onclick="window.location='http://www.kis.ac.th/'">Done</button>
			</div>
    </div>
	</div>
@endsection
