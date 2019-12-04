var themeColor = randomColor();

$(':root').css("--theme-color", "rgb("+themeColor+")");

function randomColor() {
    var colorlist = [
        '220,20,60', //crimson
        '139,0,0', //brick red
        '255,105,180', //hot pink
        '199,21,133', //medium violet red
        '255, 163, 0', //orange
        '255,69,0', //orange red
        '255,215,0',
        '23, 165, 137',
        '155, 89, 182',
        '231, 76, 60',
        '52, 152, 219',
        '22, 160, 133',
        '165, 105, 189'
    ];
    var colorindex = Math.floor(Math.random() * colorlist.length);
    console.log(colorindex);
    return colorlist[colorindex];
}

function startBackground() {
    $('#backgroundDiv').polygonizr({
        restNodeMovements: 0.5,
        numberOfNodes: 50,
        duration: 4,
        numberOfUnconnectedNode: 0,
        ConnectUnconnectedNodes:true,
        ConnectUnconnectedNodesDistance: 150,
        nodeDotSize: 2.5,
        nodeEase: "easeInOut",
        nodeFancyEntrance: false,
        randomizePolygonMeshNetworkFormation: true,
        nodeRelations: 3,
        animationFps:30,
        nodeDotColor: "0,0,0",
        nodeFillColor: themeColor,
        nodeFillAlpha: 0.6,
        nodeLineAlpha: 0.5,
        nodeDotAlpha: 1.0,
        nodeFillSpace: true,
        nodeDotPrediction: 0,
        nodeOverflow: true,
        nodeGlowing: false,
        canvasWidth: $(this).width(),
        canvasHeight: $(this).height(),
        canvasPosition: "absolute",
        canvasTop: "0",
        canvasBottom: "auto",
        canvasRight: "auto",
        canvasLeft: "auto"
    });
}