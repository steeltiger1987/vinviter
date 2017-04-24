<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,500,600,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	@yield('head')
	<title>Vinviter: Discover Local Events in your Area</title>
	<meta name="application-name" content="Discover Local Events in your Area">
	<meta name="description" content=" Vinviter helps you to discover & locate the most amazing events in your local area. You can also build and publish your own events & impress thousands of people!"/>
	<meta name="keywords" content=" events this weekend, events near me, events near me this weekend , festival calendar, what's going on near me today, create an event , event creator , make my event , set an event , events in my area , event center , conference room, venues for rent , event website , event space chicago "/>
</head>
<body>
	<div id="wrapper">
		@include('layouts.header')
		@yield('content')
		@include('layouts.footer')
		<script src="{{ asset('js/all.js') }}"></script>
		@yield('scripts')
	</div>
	<div class="loading-overlay">
		<span><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i> Please wait</span>
	</div>
</body>
</html>