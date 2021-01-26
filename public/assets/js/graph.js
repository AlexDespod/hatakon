let stores = {
    metro: [],
    novus: [],
    auchan: [],
};
function setData(mass, element, date) {
    let obj = {
        y: element.price,
        x: date / Math.pow(10, 12),
        brand: element.brand,
    };
    // if (metro[element.brand]) {
    //     console.log(1);
    // } else {
    if (!Array.isArray(mass[element.title])) {
        mass[element.title] = [obj];
    } else {
        mass[element.title].push(obj);
    }
}
(async function () {
    const res = await fetch("api/graph");
    const data = await res.json();
    data.forEach((element) => {
        let date = Date.parse(element.created_at);
        setData(stores[element.store], element, date);
        // console.log(metro);
        // throw new Error("ss");
    });
    // var aData = reponse.d;
    // let aLabels = Object.keys(stores["metro"]);
    let bigDataset = [];
    let nodes = {
        metro,
        auchan,
        novus,
    };
    // console.log(aLabels);
    for (store in stores) {
        nodes[store] = document.getElementById(store);
        let datasets = [];
        for (name in stores[store]) {
            let randomColor = Math.floor(Math.random() * 16777215).toString(16);
            datasets.push({
                label: name,
                lineTension: 0,
                fill: false,
                borderColor: "#" + randomColor,
                data: stores[store][name],
            });
        }
        bigDataset.push(datasets);
    }
    console.log(bigDataset);

    Chart.defaults.global.animationSteps = 50;
    Chart.defaults.global.tooltipYPadding = 16;
    Chart.defaults.global.tooltipCornerRadius = 0;
    Chart.defaults.global.tooltipTitleFontStyle = "normal";
    Chart.defaults.global.tooltipFillColor = "rgba(0,160,0,0.8)";
    Chart.defaults.global.animationEasing = "easeOutBounce";
    Chart.defaults.global.responsive = true;
    Chart.defaults.global.scaleLineColor = "black";
    Chart.defaults.global.scaleFontSize = 16;

    let options = {
        maintainAspectRatio: true,
        legend: {
            display: true,
            position: "left",
            align: "start",
            labels: {
                boxWidth: 20,
                fontColor: "black",
            },
        },
    };
    let i = 0;
    for (node in nodes) {
        let graphData = {
            labels: [],
            datasets: bigDataset[i],
        };

        let lineChart = new Chart(nodes[node], {
            type: "line",
            data: graphData,
            options,
        });
        i++;
    }
    // console.log(ctx);
})();
// function OnSuccess_(reponse) {

// var aDatasets1 = aData[1];
// var aDatasets2 = aData[2];
// var aDatasets3 = aData[3];
// var aDatasets4 = aData[4];
// var aDatasets5 = aData[5];
// var lineChart = new Chart(ctx).Line(lineChartData, {
//     // bezierCurve: true,
//     // chartArea: { width: "62%" },
//     // responsive: true,
//     // pointDotRadius: 10,
//     // scaleShowVerticalLines: false,
//     // scaleGridLineColor: "black",
// });
