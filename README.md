# moodle-tiny_fontfamily

[![Moodle Plugin CI](https://github.com/finspire-fi/moodle-tiny_fontfamily/actions/workflows/ci.yml/badge.svg)](https://github.com/finspire-fi/moodle-tiny_fontfamily/actions/workflows/ci.yml)

A [TinyMCE](https://www.tiny.cloud/) editor plugin for Moodle that adds a font family picker to the toolbar and Format menu, letting users change the font family of selected text.

## Features

- Toolbar button and Format menu entry for choosing a font family
- The list of available font families is configurable by an administrator
- If fewer than two font families are configured, the picker is hidden rather than shown empty

## Requirements

- Moodle 4.1 (2022112800) or later

## Installation

Copy (or clone) this repository into your Moodle installation at:

```
lib/editor/tiny/plugins/fontfamily
```

Then visit *Site administration &raquo; Notifications* to complete the installation.

## Settings

Go to *Site administration &raquo; Plugins &raquo; Text editor &raquo; TinyMCE editor &raquo; Font family* to configure:

| Setting | Description |
| --- | --- |
| Font families | The list of font families offered in the picker, one per line |
| License key | Your Finspire license key for this plugin |

## Licensing

This plugin requires a valid license key from [Finspire](https://finspi.re). A scheduled task checks the license against the Finspire licensing server roughly once a week; the exact day/time is randomised per installation so that thousands of sites don't all query the server at once. Validation status (valid/invalid, expiry date, last checked) is shown on the settings page.

## License

Licensed under the [GNU GPL v3 or later](https://www.gnu.org/copyleft/gpl.html).
