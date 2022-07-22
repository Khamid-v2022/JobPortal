<!DOCTYPE html>

<html lang="en">
	<!--begin::Head-->
	<head>
		<title>Job Portal</title>
		<meta charset="utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
	
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="{{ asset('plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/style.bundle.css') }}" href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->

		<link rel="stylesheet" href="{{ asset('css/app.css') }}" >
	</head>
	<!--end::Head-->
	
    <!--begin::Body-->
	<body id="kt_body" class="bg-body">
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Content-->
			@yield('content')
			<!--end::Content-->             		
		</div>
		<!--end::Root-->
		
        
		<!--begin::Javascript-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('js/scripts.bundle.js') }}"></script>
	    
        <script src="{{ asset('js/app.js') }}"></script>
		<!--end::Javascript-->
	
        
        @stack('scripts')

	</body>
	<!--end::Body-->
</html>