# StarRating — MediaWiki Extension

![Example Usage](docs/images/readme_001.png)

**Star Rating** displays a five-star rating widget within a MediaWiki page,  
allowing users to visually rate content with stars—just like "Five stars!".

## ✨ Features

- Aggregates rating votes from multiple users,  
  and displays the average score and distribution.

- Stores screen context, tag `id` attributes, and per-user rating data in the database.  
  ![Vote](docs/images/readme_002.png)

- Supports placing multiple rating widgets on a single page.

## 📦 Installation

1. **Download the extension**  
   You can download the entire extension as a ZIP file from the GitHub Releases page:  
   or click the green **Code** button on the repository page and choose **Download ZIP**.

2. Extract the contents into your `extensions/` directory:

   extensions/
   └── StarRating/

3. Add the following to your LocalSettings.php:

   wfLoadExtension( 'StarRating' );

Note: Make sure the folder name matches the extension name ("StarRating"),  
and that the `extension.json` file is present in the root of the folder.

If you're using a custom image set, update the image paths in the JavaScript file accordingly.

This extension follows a modular structure.  
For details, refer to the source files directly (e.g. `includes/`, `modules/`, etc.).

## ⚙️ Configuration

The extension uses default star images located in the `images/` directory:

- star-full.png  
- star-half.png  
- star-empty.png

To use your own images, replace these files or update the image paths in `StarRating.js`.

You can also adjust the hover and click behavior by modifying the JavaScript logic in `modules/StarRating.js`.

## 🧩 Usage

To display the rating feature provided by this extension,  
insert the following tag into the desired page:

```html
<StarRating id="id_name">
```

## 💾 Database Storage Specification

User-submitted rating results are stored in the database table `star_rating`  
with the following structure:

- Page ID (`page_id`)  
- Tag ID (`tag_id`)  
- User ID (`user_id`)  
- Rating score (`rating`)

## ⚙️ Tag Parameters

The following parameters can be specified:

| Parameter      | Description                                                                 |
|----------------|-----------------------------------------------------------------------------|
| `id`           | Required. Used as a unique identifier for the tag.                         |
| `digit`        | Number of decimal places shown in the rating score. `0`–`4` (default: `1`) |
| `star_size`    | Size of the displayed stars (default: `16px`)                              |
| `clear_cache`  | Clears the page cache when rendering the tag (default: `false`)  <br/> May slow down page display. |

```html
Store 1<br/>
<StarRating id="store2" digit="2" star_size="20" clear_cache="true">
Store 2<br/>
<StarRating id="store1" digit="2" star_size="20">
```
  ![tags usage](docs/images/readme_001.png)

## 📜 License

This extension is released under the GNU General Public License v2 or later:  
https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

## 🤝 Contributing

Bug reports, feature requests, and pull requests are welcome.  
Feel free to fork and improve!













