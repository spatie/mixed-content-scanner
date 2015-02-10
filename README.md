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




