<!DOCTYPE html>
<html lang="ru">

<head>
    <title>Hakaton 2021</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

    <link rel="stylesheet" href="{{asset('css/flex.css')}}">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
<header>
    <h1>L.A.MASTERS</h1>
    <nav>
        <ul class="nav_links">
            <li>
                <a href="/">MAIN</a>
            </li>
            <li>
                <a href="/top">TOP 10</a>
            </li>
            <li>
                <a href="/graph">GRAPHIC</a>
            </li>
        </ul>
    </nav>
</header>
<div class="wrapper">

    <div style="width: 1200px;height:1600px;">
        <h1>METRO</h1>
        <h2>price per kg</h2>
        <canvas id="metro" width="500" height="200" ></canvas>
        <h1>NOVUS</h1>
        <h2>price per kg</h2>
        <canvas id="novus" width="500" height="200" ></canvas>
        <h1>AUCHAN</h1>
        <h2>price per kg</h2>
        <canvas id="auchan" width="500" height="200" ></canvas>
    </div>

</div>

    <div>hello</div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="{{asset('js/graph.js')}}"></script>

</body>
</html>
