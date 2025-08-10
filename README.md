# StarRating — MediaWiki Extension

A lightweight MediaWiki extension that adds interactive star rating functionality to wiki pages.  
Supports half-stars, dynamic image switching, and robust event handling for a smooth user experience.

## ✨ Features

- ⭐ Mouseover, mouseleave, and click event logic  
- 🌗 Half-star rendering with image switching  
- 🖼 Customizable star images (full, half, empty)  
- 🔧 Easy integration via parser functions  
- 🧪 Designed for edge-case reliability and UI consistency


## 📦 Installation

1. **Download the extension**  
   You can download the entire extension as a ZIP file from the GitHub Releases page:  
   https://github.com/yourusername/StarRating/releases  
   or click the green **Code** button on the repository page and choose **Download ZIP**.

2. Extract the contents into your `extensions/` directory:

   extensions/
   └── StarRating/

3. Add the following to your LocalSettings.php:

   wfLoadExtension( 'StarRating' );

4. Run the update script to create the required database table:
php maintenance/update.php

This will automatically create the `star_rating` table used to store rating data.

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

Insert the star rating widget using the parser function:

{{#starrating: page=ExamplePage | rating=3.5 }}

Parameters:

- page: The page to associate the rating with  
- rating: Initial rating value (supports half-stars, e.g. 4.5)

Example:

{{#starrating: page=KyotoCycling | rating=4.0 }}

## 📜 License

This extension is released under the GNU General Public License v2 or later:  
https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

## 🤝 Contributing

Bug reports, feature requests, and pull requests are welcome.  
Feel free to fork and improve!













