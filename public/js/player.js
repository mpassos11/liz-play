$(document).ready(function () {
    const div = $('#clappr-player');
    const url = div.data('link');
    const poster = div.data('poster');

    console.log(url);
    var player = new Clappr.Player({source: url, parentId: "#clappr-player"});
});