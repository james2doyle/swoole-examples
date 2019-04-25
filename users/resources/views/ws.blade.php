<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Swoole 😘</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Laravel Swoole 😘
                </div>

                <p>Open your chrome console</p>

                <div class="code">
                    <pre><code id="output"></code></pre>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const output = document.getElementById('output');

                const socket = new WebSocket('ws://' + window.location.host);

                socket.addEventListener('open', (e) => {
                    console.log(e);
                });

                socket.addEventListener('close', (e) => {
                    console.log(e);
                });

                socket.addEventListener('error', (e) => {
                    console.log(e);
                });

                // Listen for messages and push to our array
                socket.addEventListener('message', ({ data }) => {
                    console.log(data);
                    // const data = JSON.parse(data);
                    output.innerHTML = data;
                });
            });
        </script>
    </body>
</html>
