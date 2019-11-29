/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

var secs = 4;

$(document).ready(function() {
    window.backgroundImages.forEach(function(img) {
        new Image().src = img;
        // caches images, avoiding white flash between background replacements
    });
    backgroundSequence();

    $("#point-form").submit(function (event) {
        event.preventDefault();
        $("#point-form button").prop("disabled", true);
        $("#point-form button").text("درحال بارگذاری...");

        var mobile = $("#mobile").val();
        var code = $("#code").val();
        axios.post('/get-info',{mobile, code})
        .then(function (response) {
            Swal.fire({
                title: response.data.message,
                type: "success",
                confirmButtonText: 'تایید',
            });
            $("#code").val(response.data.id);
        }).catch(function (error) {
            console.error(error);
            Swal.fire({
                title: error.response.status == 422 ? error.response.data.errors[Object.keys(error.response.data.errors)[0]][0] : "خطایی رخ داد",
                type: "error",
                confirmButtonText: 'تایید',
            });
        }).then(function () {
            $("#point-form button").prop("disabled", false);
            $("#point-form button").text("بررسی");
        });
    });

    $("#code").keyup(() => $("#mobile").val(""));
    $("#mobile").keyup(() => $("#code").val(""));
});

function backgroundSequence() {
    window.clearTimeout();
    var k = 0;
    for (i = 0; i < window.backgroundImages.length; i++) {
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
