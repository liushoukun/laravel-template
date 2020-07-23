
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>{{ config('app.name') }}</title>

  <link rel="stylesheet" href="{{ mix('css/app.css','manage') }}">
</head>
<body>
  <div id="app"></div>


  {{-- Load the application scripts --}}
  <script src="{{ mix('js/vendor.js','manage') }}"></script>
  <script src="{{ mix('js/manifest.js','manage') }}"></script>
  <script src="{{ mix('js/main.js','manage') }}"></script>
</body>
</html>
