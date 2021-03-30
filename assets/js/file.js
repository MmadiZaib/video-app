const $ = require('jquery');

$('input[type="file"]').change(function (e) {
    var fileName = e.target.files[0].name;
    console.log(e.target.files[0]);
    $('.custom-file-label').html(fileName);
});