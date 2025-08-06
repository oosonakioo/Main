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
          <h1>Payment</h1>
        </div>
      </div>
      <div class="content">
        <div class="row">
          <span>Invoice :</span> <b>{{ $invoice }}</b>
        </div>
        <div class="row">
          <span>Student Name :</span> <b>{{ $studentname }}</b>
        </div>
        <div class="row">
          <span>Due Date :</span> <b>{{ $duedate }}</b>
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
        </div>
      </div>
      <button type="button" class="btn btn-blue" data-toggle="modal" data-target="#termsModal" onclick="resetmodal();">PAY</button>
    </div>
	</div>

	<!-- Modal Term and Condition -->
	<!-- Terms and conditions modal -->
	<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="Terms and conditions" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h3 class="modal-title">Terms and conditions</h3>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
	            </div>

	            <div class="modal-body">
								{!! $terms !!}
								<br>
								<input type="checkbox" name="terms" id="terms" onchange="activateButton(this)">  I Agree Terms & Conditions
	            </div>

	            <div class="modal-footer">
								<form id="payment-form" method="post" action="{{ config('setting.payment-url') }}" role="form">
									{!! csrf_field() !!}
									<input type="hidden" id="encryptionstr" name="encryptionstr" value="{{ $encryptionstr }}">
									<input type="hidden" name="merchantId" value="{{ config('setting.payment-merchantid') }}">
									<input type="hidden" name="currCode" value="{{ config('setting.payment-currcode') }}" >
									<input type="hidden" name="amount" value="{{ number_format((float)$totalwithfee, 2, '.', '') }}" >
									<input type="hidden" name="orderRef" value="{{ $invoice }}">
									<input type="hidden" name="successUrl" value="{{ Helper::url(config('setting.payment-successurl')) }}">
									<input type="hidden" name="failUrl" value="{{ Helper::url(config('setting.payment-failurl')) }}">
									<input type="hidden" name="cancelUrl" value="{{ Helper::url(config('setting.payment-cancelurl')) }}">
									<input type="hidden" name="payType" value="{{ config('setting.payment-paytype') }}">
									<input type="hidden" name="lang" value="{{ config('setting.payment-lang') }}">
									<input type="hidden" name="TxType" value="{{ config('setting.payment-txttype') }}">
									<input type="hidden" name="remark" value="{{ $custcode }} : {{ $studentname }}">
									<button type="submit" class="btnmodal btn-primary" id="agreeButton" disabled >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PAY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
									<button type="button" class="btnmodal btn-default" id="disagreeButton" data-dismiss="modal">DISAGREE</button>
								</form>
	            </div>
	        </div>
	    </div>
	</div>

	<script>
		function resetmodal() {
			$('#terms').prop('checked', false);
			$('#agreeButton').prop("disabled", true);
		}
		function activateButton(element) {
			if(element.checked) {
				$('#agreeButton').prop("disabled", false);
			 }
			 else  {
				$('#agreeButton').prop("disabled", true);
			}
		}

		/*$(function () {
				$('#payment-form').on('submit', function (e) {
					console.log('xx');
		      var url = "{{ url('confirm') }}";
					$.ajax({
		        type: 'POST',
		        url: url,
		        data: $('#payment-form').serialize(),
		      }).done(function(response){
		        //window.location.href = "{{ Helper::url('') }}";
		      });
		      e.preventDefault();
				});
		});*/


	</script>
@endsection
