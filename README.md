# PDF-generator
Laravel-PDF generator using template(Fpdi library used)

# Fpdf(Laravel package)
Using [FPDF](http://fpdf.org/). made easy with Laravel. See FPDF homepage for more information about the usage.

## Installation using Composer
` composer require codedge/laravel-fpdf `

## Configuration
Run
`php artisan vendor:publish --provider="Codedge\Fpdf\FpdfServiceProvider" --tag=config`
to publish the configuration file to `config/fpdf.php`.

## Usage
```
// app/Http/routes.php | app/routes/web.php

Route::get('/', function (Codedge\Fpdf\Fpdf\Fpdf $fpdf) {

    $fpdf->AddPage();
    $fpdf->SetFont('Courier', 'B', 18);
    $fpdf->Cell(50, 25, 'Hello World!');
    $fpdf->Output();
    exit;
});
```

## Use in Laravel Vapor
If you want to use [Laravel Vapor](https://vapor.laravel.com/) to host your application, [a special header](https://docs.vapor.build/1.0/projects/development.html#binary-responses) needs to be attached to each response that FPDF returns to your browser. To enable the use of this header, add the following environment variable to the Vapor environment file:

`FPDF_VAPOR_HEADERS=true`
