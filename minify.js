var CleanCSS = require('clean-css');
var fs = require('fs');

fs.readFile('style.css', 'utf8', function(err, data) {
    if (err) {
        console.log(err);
    } else {
        var output = new CleanCSS().minify(data);
        fs.writeFile('style.min.css', output.styles, function(err) {
            if (err) {
                console.log(err);
            } else {
                console.log('CSS Minification complete.');
            }
        });
    }
});
