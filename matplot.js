var canv = document.getElementById("cv");
var cont = canv.getContext("2d");


//var test = document.getElementById("test");
var countnode = nodes.length;
var contlen = Math.round(1100/countnode);
contlen = contlen>50 ? 50 : (contlen < 20 ? 20 : contlen);
var contlen1 = 100;
var hend = (countnode+1)*contlen-contlen+contlen1;
var vend = (countnode+1)*contlen-contlen+contlen1;


function plothline(i)
{
    cont.beginPath();
    cont.moveTo(contlen1,i*contlen-contlen+contlen1);
    cont.lineTo(hend,i*contlen-contlen+contlen1);
    cont.lineWidth=2;
    cont.strokeStyle = "#888";
    cont.stroke();
}
function plotvline(j)
{
    cont.beginPath();
    cont.moveTo(j*contlen-contlen+contlen1,contlen1);
    cont.lineTo(j*contlen-contlen+contlen1,vend);
    cont.lineWidth=2;
    cont.strokeStyle = "#888";
    cont.stroke();
}
function plothline1(i)
{
    cont.beginPath();
    cont.moveTo(contlen1,i*contlen-contlen+contlen1);
    cont.lineTo(0,i*contlen-contlen+contlen1);
    cont.lineWidth=1;
    cont.strokeStyle = "#eee";
    cont.stroke();
}
function plotvline1(j)
{
    cont.beginPath();
    cont.moveTo(j*contlen-contlen+contlen1,0);
    cont.lineTo(j*contlen-contlen+contlen1,vend);
    cont.lineWidth=1;
    cont.strokeStyle = "#eee";
    cont.stroke();
}

function plotcircle(i,j,weight)
{
    cont.beginPath();
    cont.arc( contlen*(i+1.5)-contlen+contlen1, contlen*(j+1.5)-contlen+contlen1, contlen*Math.min(0.5,0.5*Math.sqrt(weight)), 0, 2 * Math.PI, false);
    cont.fillStyle = 'blue';
    cont.fill();
}
function plottext(x,y,st){
    cont.font=contlen1/8+"px Arial";
    cont.textAlign="center";
    cont.fillStyle = 'black';
    cont.fillText(st,x,y,contlen1);
}

function plottext2(x,y,st){
    cont.font= "bolder " + (contlen1/8+2.5)+"px Arial";
    cont.textAlign="center";
    cont.fillStyle = 'black';
    cont.fillText(st,x,y,2*contlen1);
}

canv.width = (countnode+2)*contlen-contlen+contlen1;
canv.height = (countnode+2)*contlen-contlen+contlen1;


plotAll();

function plotAll()
{
    cont.clearRect(0,0,canv.width,canv.height);

    for (var i = 1 ; i != countnode+2 ; i++ )
    {
        plothline(i);
        plotvline(i);
        plothline1(i);
        plotvline1(i);
    }

    plottext( (0.75)*contlen1  ,  0.25*contlen1  ,"Target");
    plottext( (0.25)*contlen1  ,  0.85*contlen1  ,"Source");
    for (var i = 0 ; i < countnode ; i++)
    {
        plottext(  0.5*contlen1 ,  (i+1.5)*contlen -contlen+contlen1 ,nodes[i].id);
    }

    cont.save();
    cont.translate((0.5)*contlen1  ,  0.5*contlen1);
    cont.rotate(-Math.PI/2);
    for (var i = 0 ; i < countnode ; i++)
    {
        plottext( 0,  (i+1)*contlen -0.5*contlen+0.5*contlen1,nodes[i].id);
    }
    cont.restore();

    for (var i = 0 ; i < links2.length ; i++)
    {
        plotcircle(links2[i].source,links2[i].target,links2[i].weight);
    }

}

canv.addEventListener("mousemove", mouseOverCanvas, false);

function mouseOverCanvas(e){
    plotAll();
    var pos = findPos(this);
    var x = e.pageX - pos.x;
    var y = e.pageY - pos.y;

    var ii = Math.ceil((x-contlen1)/contlen)-1;
    var jj = Math.ceil((y-contlen1)/contlen)-1;
    if ( (ii >-0.5) && (jj >-0.5) )
    {
        var texttodisplay = nodes[ii].id+" -> "+nodes[jj].id;
        plottext2(x,y-20,nodes[jj].id);
        plottext2(x,y," -> ");
        plottext2(x,y+20,nodes[ii].id);
    }
}

function findPos(obj) {
    var curleft = 0, curtop = 0;
    if (obj.offsetParent) {
        do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
        } while (obj = obj.offsetParent);
        return { x: curleft, y: curtop };
    }
    return undefined;
}

