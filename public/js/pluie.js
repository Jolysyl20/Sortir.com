var nbimg = 18;
var typeimg;
var coordx = new Array(nbimg);
var coordy = new Array(nbimg);
var vity = new Array(nbimg);
var temx = new Array(nbimg);
var decx = new Array(nbimg);
var img = 1;

function InitNeige() {

    var date = new Date();
    var jour = date.getDate();
    var mois = date.getMonth() + 1;

    if (((mois == 12) && (jour >= 21)) || ((mois == 3) && (jour < 21)) || (mois < 3)) {
        typeimg = "flocon";
    }
    if (((mois == 3) && (jour >= 20)) || ((mois == 6) && (jour < 20)) || ((mois > 3) && (mois < 6))) {
        typeimg = "fleur";
    }
    if (((mois == 6) && (jour >= 21)) || ((mois == 9) && (jour < 21)) || ((mois > 6) && (mois < 9))) {
        typeimg = "soleil";
    }
    if (((mois == 9) && (jour >= 22)) || ((mois == 12) && (jour < 22)) || ((mois > 9) && (mois < 12))) {
        typeimg = "feuille";
    }


    var tailley = document.body.clientHeight;
    var taillex = document.body.clientWidth;

    var styl = 'position:absolute;left:0px;top:0px;width:1px;height:1px;';

    for (var i = 0; i < nbimg; i++) {

        var chaine = document.createElement('div');
        chaine.id = 'image' + i
        chaine.setAttribute('style', styl)

        var flocon = document.createElement('img');
        flocon.src = 'gif/' + typeimg + img + '.gif';

        chaine.appendChild(flocon);
        document.body.appendChild(chaine);

        coordx[i] = 80 + (Math.random() * taillex - 160);
        coordy[i] = i * (tailley / (nbimg - 1));
        vity[i] = img;
        temx[i] = (Math.random() * 19);
        decx[i] = 0;
        img += 1;
        if (img > 3) {
            img = 1
        };
    }
    neige()
}

function neige() {

    var tailley = document.body.clientHeight;
    var taillex = document.body.clientWidth;
    var offsety = document.body.scrollTop || document.documentElement.scrollTop;
    var offsetx = document.body.scrollLeft || document.documentElement.scrollLeft;

    for (var i = 0; i < nbimg; i++) {

        document.getElementById('image' + i).style.top = coordy[i] + 'px';
        document.getElementById('image' + i).style.left = coordx[i] + 'px';

        temx[i] += 1
        if (temx[i] > 20) {
            decx[i] = 1 - (Math.random() * 2);
            temx[i] = 0;
        }
        coordx[i] += decx[i];
        coordy[i] += vity[i];
        maxi = tailley + offsety;
        if (coordy[i] > maxi) {
            coordx[i] = 80 + (Math.random() * taillex - 160);
            coordy[i] = -100;
        }
    }
    setTimeout(neige, 50);
}

typeof window.addEventListener == 'undefined' ? attachEvent("onload", InitNeige) : addEventListener("load", InitNeige, false);

// end hiding -