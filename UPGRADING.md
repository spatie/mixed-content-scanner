# Upgrading

Because there are many breaking changes an upgrade is not that easy. 
There are many edge cases this guide does not cover. 
We accept PRs to improve this guide.

## From 4.0 to 5.0

- `spatie/crawler` is updated to `^8.0`. In the `MixedContentObserver` provided by this package, a new parameter `linkText` was added to several methods. If you extend that observer, you should add that parameter to your methods as well.

## From 2.0 to 3.0

- `spatie/crawler` is updated to `^4.0`. This version made changes to the way custom `Profiles` and `Observers` are made. Please see the [UPGRADING](https://github.com/spatie/crawler/blob/master/UPGRADING.md) guide of `spatie/crawler` to know how to update any custom crawl profiles or observers - if you have any.


## From 1.0 to 2.0

- We're now using `spatie/crawler` v3. Please check its [UPGRADING.md](https://github.com/spatie/crawler/blob/master/UPGRADING.md#from-v2-to-v3)
for the information to update loggers.
