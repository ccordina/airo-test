<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="api-token" content="{{ $token ?? '' }}">
        <meta name="currencies" content="{{ json_encode(array_map(fn($c) => ['value' => $c->value, 'label' => $c->label()], $currencies)) }}">
        <title>Travel Insurance Calculator</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <main class="mx-auto flex min-h-screen max-w-xl items-center px-4 py-12">
            <div class="w-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <header class="mb-8">
                    <h1 class="text-2xl font-semibold tracking-tight">Travel Insurance Calculator</h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Enter your trip details to get a quote.
                    </p>
                </header>

                <div id="app"></div>
            </div>
        </main>
    </body>
</html>
