<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Web Service - Masa Concretos</title>

    {{-- Icon --}}
    <link rel="shortcut icon" href="zskins/rastreoporsatelite/images/favicon.ico" type="image/x-icon">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">

    {{-- Bootstrap Customized CSS --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">

    {{-- Font Awesome --}}
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    {{-- Custom CSS --}}
    <style>
        body {
            background: url("{{ asset('img/background.webp') }}") no-repeat center center fixed;
            background-size: cover;
            /* backdrop-filter: blur(2px); */
        }
    </style>
</head>

<body style="overflow-x: hidden">
    <div class="d-flex justify-content-end align-items-end vh-100 row pb-5 pe-5">
        <div class="col-md-12">
            <div class="d-flex justify-content-end align-content-end h-100">
                <div class="text-center">
                    <h1 class="fw-bold" style="font-size: 48px;">
                        Servicio Web
                    </h1>
                    <p style="font-size: 20px;">Masa Concretos</p>
                    <a
                        href="https://www.dropbox.com/s/31uovo4twcatjjh/Manual-Integracion-API-REST.pdf?dl=0" target="_blank">
                        <button type="button" class="btn btn-primary rounded-pill w-100" onclick="#">
                            Documentaci&oacute;n&nbsp;&nbsp;
                            <i class="bi bi-arrow-right-circle"></i>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
</body>

</html>
