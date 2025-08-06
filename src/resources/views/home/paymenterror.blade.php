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
					<br>
					<span style="color:red"><strong>{!! $errormsg !!}</strong>
					<br><br><br>
				</div>
			</div>
			<button type="button" class="btn btn-green" onclick="window.location='http://www.kis.ac.th/'">Done</button>
		</div>
	</div>
@endsection
