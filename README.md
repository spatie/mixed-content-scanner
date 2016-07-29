###Abandoned
This project is abandoned. Please use [this scanner ](https://github.com/bramus/mixed-content-scan) instead.


Mixed content scanner
=====================

Scan a HTTPS-site for [mixed content](https://developer.mozilla.org/en-US/docs/Security/MixedContent). The scanner itself was built by [Bramus](https://github.com/bramus/mixed-content-scan).

Installation
---
You can install the scanner via composer

```
composer global require spatie/mixed-content-scanner
```

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment you are required to send us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

Usage
---
You can start the scanner like this

```
mixed-content-scanner
```

You will be asked for an url which will be scanned for mixed content.

It's also possible to pass an url with the original command:

```
mixed-content-scanner scan https://www.bennish.net
```

Optionally you can write the results of a scan as json to an outputfile

```
mixed-content-scanner scan https://www.bennish.net --output="/your-outputfile.json"
```


Updating
---
You can upgrade to the latest version of mixed-content-scanner by executing:
```
composer global update spatie/mixed-content-scanner
```




