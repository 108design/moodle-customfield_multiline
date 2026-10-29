# Certificate Multitool – Custom Field

The text workflow in Certificate Multitool combines two Moodle plugins: course editors maintain certificate wording in course settings, and certificate designers control its layout in a shared Certificate manager template. Install the two plugins together for this workflow.
<details>
<summary>Screenshots</summary>

<table><tr><td><a href="https://raw.githubusercontent.com/108design/moodle-customfield_multiline/main/docs/screenshots/shared-text-template.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-customfield_multiline/main/docs/screenshots/shared-text-template.jpg" alt="Course text placed in a shared certificate template" width="420"></a><br>Course text placed in a shared certificate template</td><td><a href="https://raw.githubusercontent.com/108design/moodle-customfield_multiline/main/docs/screenshots/course-preview.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-customfield_multiline/main/docs/screenshots/course-preview.jpg" alt="Course certificate HTML preview" width="420"></a><br>Course certificate HTML preview</td></tr></table>

</details>


| Part | Role |
| --- | --- |
| [Custom Field](https://github.com/108design/moodle-customfield_multiline) | Stores course text, text mode and alignment choices. |
| [Cert. Element](https://github.com/108design/moodle-certificateelement_coursefieldtext) | Places those course values on the certificate template and supplies the course HTML preview. |

This repository contains the **Custom Field** part of the pair.


## Certificate Multitool set

Use the text pair for course-managed wording and the image pair for course-specific pictures in a shared certificate template. Flexible Date supplies the date formatting. Choose the parts your workflow needs.

| Plugin | Role and companion |
| --- | --- |
| [Certificate Multitool – Custom Field](https://github.com/108design/moodle-customfield_multiline) | Stores course text, text mode and alignment. Pair it with Cert. Element for certificate output. |
| [Certificate Multitool – Cert. Element](https://github.com/108design/moodle-certificateelement_coursefieldtext) | Renders that course text and supplies the course HTML preview. Requires Custom Field and Certificate manager. |
| [Course image](https://github.com/108design/moodle-customfield_courseimage) | Provides a single-image course field. Pair it with Course field image for certificate output. |
| [Course field image](https://github.com/108design/moodle-certificateelement_coursefieldimg) | Places a course image on the certificate and preserves the issued image copy. Requires Certificate manager; it accepts Course image or a native textarea field with an internal image. |
| [Flexible Date](https://github.com/108design/moodle-certificateelement_flexdate) | Formats certificate issue and expiry dates. Requires Certificate manager. |

**Text pair:** install Custom Field and Cert. Element together for the course-text certificate workflow. **Image pair:** install Course image and Course field image together for the single-image upload workflow. The image element can also use a native textarea image without Course image. Course certificate is needed for course-based issuance and the text pair’s course HTML preview.

### Moodle 5.3 qualification

Moodle 5.3 integration is provisional: it has been tested with Certificate manager (tool_certificate) 5.0.10/5.0.11 outside those versions’ declared Moodle 5.0–5.2 support range. Check the compatibility of Certificate manager and, when used, Course certificate before deploying on Moodle 5.3. The course custom fields themselves do not require Certificate manager.


## Guides for both plugins

Start with the guide for your role. The same neutral, screenshot-free guides are included in both repositories.

| Guide | Read online | Download |
| --- | --- | --- |
| Course editors: enter text, choose formatting and check the course preview | [Course editor guide](docs/COURSE_EDITOR_GUIDE.md) | [PDF](docs/COURSE_EDITOR_GUIDE.pdf) |
| Administrators and certificate designers: install the pair, create fields and configure templates | [Administrator guide](docs/ADMINISTRATOR_GUIDE.md) | [PDF](docs/ADMINISTRATOR_GUIDE.pdf) |

[Documentation index](docs/README.md)


## Set up the pair

1. Install **Certificate Multitool - Custom Field** in `customfield/field/multiline`.
2. Install a compatible **Certificate manager** (`tool_certificate`) and **Certificate Multitool - Cert. Element** in `admin/tool/certificate/element/coursefieldtext`. Complete Moodle's plugin upgrade.
3. Create the course fields, add the matching elements to a certificate template, and select that template in a **Course certificate** (`mod_coursecertificate`) activity. The administrator guide covers the full setup.

These paths are relative to Moodle's web/plugin root, inside `public/` on installations with a split web directory. Check the Certificate manager requirements in the administrator guide for your Moodle branch.

## Course field behaviour

Store several lines of plain text in a single Moodle course custom field. Enter inserts a hard line break; blank lines and literal special characters are retained. There is no rich-text editor.

This GPL release targets Moodle 4.5 through 5.3. Install this plugin in `customfield/field/multiline` and complete Moodle's upgrade. Add a **Certificate Multitool – Custom Field** field under **Site administration → Courses → Course custom fields**, then fill it in through course settings. Native required, unique, locked and visibility settings apply. A saved empty value suppresses the default. Native course backup/restore preserves the value, text mode and alignment.

Use the companion [**Certificate Multitool - Cert. Element**](https://github.com/108design/moodle-certificateelement_coursefieldtext) to control font, size, colour, width, position and left/centre/right alignment and line height in Certificate manager. Plain text is the default and displays HTML tags literally. Course settings offer **Text mode → Limited HTML** for each course value, entered as source without an editor. Allowed tags are `strong`, `b`, `em`, `i`, `u`, `small`, `ul`, `ol`, `li`, `br` and `p`. All attributes, CSS and media are removed; executable content is discarded. Use HTML `br` or `p` for breaks in this mode. Long lines can wrap within the certificate element width.

Changing Text mode keeps the raw source. New certificate issues save the selected mode with the content; existing issues keep their saved mode.

Choose **Text alignment** separately for each course field: **Use template alignment** (default), **Left**, **Centre** or **Right**, for either text mode. An explicit choice aligns text inside the block positioned by the template. Set the block width and position, font and line height in the template once; the same template can serve different course alignments. List items retain their left-aligned hanging indents. New issues save the chosen mode and alignment with the text.

On upgrade, existing saved values retain their previous Plain text or Limited HTML interpretation and inherit template alignment. Existing issues keep their saved presentation. Defaults from earlier HTML-enabled fields keep their interpretation until a course saves its own choice.

**Vertical alignment** follows the same default/override model: choose **Inherit from template**, **Top**, **Middle** or **Bottom** for each field in course settings; the certificate element supplies a **Top**, **Middle** or **Bottom** default. Optionally set **Area height (mm)** in the element. Position Y is then the upper edge of the area: Top aligns text there, Bottom aligns it to Position Y plus the height. A one-line value and a multiline value can use the same reserved space. The PDF renderer rejects text that does not fit rather than clipping it; enlarge the area or adjust text, width, font size or line height.

Leave Area height empty to preserve legacy anchoring: Top starts at Position Y and Bottom ends at Position Y. Existing elements have no area height, so their positions remain unchanged. Issued exports retain explicit course choices; inherited choices and layout follow the current template when PDFs are regenerated. Existing stored PDFs remain unchanged.

For certificates, **Middle** requires Certificate Multitool – Cert. Element 0.7.0-beta or later. Choose **Middle** for vertical centring within a fixed-height text area. The whole block is centred, so one-line and two-line titles share the same vertical midpoint. Set an Area height in the certificate element first; a missing height is reported instead of silently changing the alignment. Course choices override the element default in both text modes. Existing values and issued exports keep their previous choices.

## Smaller extra text

In course settings, choose **Limited HTML** and save text such as `<small>Advanced Training</small><br>Certificate`, or `Certificate<br><small>Advanced Training</small>`. **Small text size (%)** in the certificate element controls its size from 10 to 100 percent of the base font size (default 60%). At 45 pt, a 60% extra line is 27 pt. The same element can place the extra line above or below the main line; the entire block still uses the configured area height and vertical alignment. Nested small tags do not shrink further. User attributes and CSS remain removed. Requires Certificate Multitool - Cert. Element 0.8.0-beta and Custom Field 0.7.0-beta or later.

Existing saved Plain text and Limited HTML values keep their interpretation on upgrade, including old HTML that happened to contain small tags. Save a course value again explicitly in Limited HTML mode to enable small. Partial updates without a submitted text mode preserve the previous interpretation. New issues retain the selected HTML semantics; existing issues retain their saved interpretation. The percentage is a template layout setting used again when a PDF is regenerated.


## Licence

GNU GPL v3 or later. See [LICENSE.md](LICENSE.md). No product activation is required.

Available free of charge under the terms of the applicable license.
