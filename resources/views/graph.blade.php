<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={device-width}, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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

    <div>hello</div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="{{asset('js/graph.js')}}"></script>

</body>
</html>
