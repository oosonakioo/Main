@extends('layouts.payment')

@section('content')
	<div class="container">
    <div class="box">
      <div class="header">
        <div class="logo">
          <a href="http://www.kis.ac.th/" target="_blank">
            <img src="{{ asset('images/svg/lg_kis.svg') }}" alt="kis">
          </a>
        </div>
        <div class="header-text">
          <img src="{{ asset('images/svg/ic_success.svg') }}" alt="kis">
          <h1>Payment Paid</h1>
        </div>
      </div>
			<div class="content">
	      <div class="row">
	        <span>Thank you, Your payment for the invoice #{{ $invoice }} was already made on {{ $duedate }}.</span>
	      </div>
				<hr>
			</div>
      <button type="button" class="btn btn-green" onclick="window.location='http://www.kis.ac.th/'">Done</button>
    </div>
	</div>
@endsection
