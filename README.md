# pagemaker

A project to build webpages in OOP style with a plug-in architecture

## Project setup

Standard config files for linting and testing are copied into place from a GitHub repository called
[utility](https://github.com/douglasgreen/utility). See that project's README page for details.

## Usage

The page builder provides a set of classes to assemble an HTML page.

## Installing assets

Widgets are installed with composer to the vendor directory of your main project. Then you should
copy their assets to the public/widgets/<name> directory with the name of your widget. You can do so
with a simple Bash script in your main project like this:

```bash
#!/bin/bash

# Set the source and destination directories
VENDOR_DIR="vendor/your-package-name"
PUBLIC_DIR="public/widgets"

# Create the destination directory if it doesn't exist
mkdir -p $PUBLIC_DIR

# Copy JavaScript files
cp -R $VENDOR_DIR/assets/js/* $PUBLIC_DIR/js/

# Copy CSS files
cp -R $VENDOR_DIR/assets/css/* $PUBLIC_DIR/css/

# Copy image files
cp -R $VENDOR_DIR/assets/images/* $PUBLIC_DIR/images/

echo "Assets copied successfully!"
```
