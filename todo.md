# TODOS

## Crawl Manager

### Queue New URLs Based on Number of Pages Crawled on Domain

We want a negative weight associated with a URL which increases for each URL that has been crawled on that domain already.

That is, we want a policy which prefers to look at new domains rather than existing, well crawled ones (breadth-first crawling).

We'll need a counting Bloom Filter or similar for this.

### Persist Frontier to Disk

The frontier is going to become too large to fit in memory (it may even become too big for a single disk), and apart from that 
there's a desire for data security - so we'll flush the frontier to disk when it reaches a set size, then reload a new "working frontier"
which crawlers will draw from. If that *working frontier* is running dry and we haven't yet persisted the current frontier to disk
then we'll do that to rebuild and ensure we have plenty of URLs to crawl.

## Crawler

### Rewrite Test CURL MULTI code to be Neater

This code is a test at present and needs to be properly integrated with proper **HEAD**, **robots.txt** and **sitemap.xml** checks.