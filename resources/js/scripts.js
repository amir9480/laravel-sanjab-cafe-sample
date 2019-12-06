
var secs = 4;
$(document).ready(function() {
    window.backgroundImages.forEach(function(img) {
        new Image().src = img;
    });
    backgroundSequence();
});

function backgroundSequence() {
    window.clearTimeout();
    var k = 0;
    for (var i = 0; i < window.backgroundImages.length; i++) {
        setTimeout(function() {
            document.getElementById("animated-bg").style.background =
                "url(" + window.backgroundImages[k] + ") no-repeat center center";
            document.getElementById("animated-bg").style.backgroundSize =
                "cover";
            if (k + 1 === window.backgroundImages.length) {
                setTimeout(function() {
                    backgroundSequence();
                }, secs * 1000);
            } else {
                k++;
            }
        }, secs * 1000 * i);
    }
}

window.numberFormat = function (number, seperator=',') {
    return String(number).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1" + seperator);
};
