<!DOCTYPE html>
<html lang="zxx">
<head>
	@include('Buyers.layouts.head')	
</head>
<body class="js">
	
	<!-- Preloader -->
	<div class="preloader">
		<div class="preloader-inner">
			<div class="preloader-icon">
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<!-- End Preloader -->
	
	@include('Buyers.layouts.notification')
	<!-- Header -->
	@include('Buyers.layouts.header')
	<!--/ End Header -->
	@yield('main-content')
	
	@include('Buyers.layouts.footer')

</body>
</html>