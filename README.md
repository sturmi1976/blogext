# Simple TYPO3 Blog
Guide on how to set up this simple blog extension in TYPO3.

# Install TYPO3 blog extension
To install the extension in TYPO3, enter the following command in the console where the root composer.json is located.

`composer req lanius/blogext`

Next, log in to the TYPO3 backend, navigate to Administration Tools -> Maintenance -> Analyze Database, and install the database tables.

Then, create a page in your page tree to store the Blog List plugin and create a folder where the records (blogs, categories, etc.) will be stored.
In your Blog List plugin, fill out the Flexform.

Now go to Pages -> General -> Sets (Site Sets) and add the Site Set for the Blog Extension.

Under Settings -> Edit Settings, fill in the fields related to the Blog Extension.

## Add RSS feed
Create a page in the page tree where you add the Blog: RSS Feed plugin. After adding the RSS plugin, fill out the Flexform fields in the plugin. Then, you can integrate the feed into the head using the following TypoScript.

```typoscript
page.headerdata.150 = TEXT
page.headerdata.150.value = <link rel="alternate" type="application/rss+xml" title="My blog" href="https://example.com/rss-feed">

## Links
| Links | Description |
|-------|-------------|
|[https://extensions.typo3.org/extension/blogext](Blog Extension - TER)|Desc|