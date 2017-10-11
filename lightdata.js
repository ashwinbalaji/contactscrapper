var contactLink;
var text;
var fileName;
var fs = require('fs');
function scrapeWikiLink() {
    console.log('citeObject1');
    var imageLink = casper.evaluate(function () {
        var citeObject = document.querySelectorAll('cite');

        for (var i = 0; i < 2; i++) {
            var url = citeObject[i].innerText;
            if (url.match("contact")) {
                console.log(url);
                break;
            }
            var url = citeObject[i].innerText;

        }
        return url;

    });

    if (imageLink.match("http")) {
        contactLink = imageLink;
    } else if(imageLink.match("https")) {
        contactLink = 'https://' + imageLink;
    }else{
        contactLink = 'http://' + imageLink;
    }

    this.thenOpen(contactLink).waitForText('contact', function () {
        this.capture(company + 'webpage.png');
        var getTextContent = casper.evaluate(function () {
            return document.body.textContent;
        });
        text = getTextContent;
        fileName = company + ".txt";
        fs.write(fileName, text);
        console.log('captured Contact');
        this.echo("done");
    });
}
var casper = require('casper').create({
    verbose: true,
    logLevel: "debug",
    clientScripts: [
        'jquery182.js'      // The script will be injected in remote
    ],
    pageSettings: {
        userAgent: "Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.2 (KHTML, like Gecko) Ubuntu/11.10 Chromium/15.0.874.120 Chrome/15.0.874.120 Safari/535.2",
        viewportSize: {
            width: 1920,
            height: 1080
        }
    }

});

var company = casper.cli.args[0];
var attributePartOne = casper.cli.args[1];
var attributePartTwo = casper.cli.args[2];
var country = casper.cli.args[3];

casper.start('https://www.google.co.in/', function () {

    this.fill('form[action="/search"]', {
        q: company + " " + attributePartOne + " " + attributePartTwo + " " + country
    }, true);

});
casper.then(function () {
    this.capture(company + 'google.png');
    this.echo("done")


});
casper.then(scrapeWikiLink);


casper.run();