# Certificate Multitool – Custom Field

Store several lines of plain text in a single Moodle course custom field. Enter inserts a hard line break; blank lines and literal special characters are retained. There is no rich-text editor.

This GPL beta targets Moodle 4.5 through 5.3. Install this plugin in `customfield/field/multiline` and complete Moodle's upgrade. Add a **Certificate Multitool – Custom Field** field under **Site administration → Courses → Course custom fields**, then fill it in through course settings. Native required, unique, locked and visibility settings apply. A saved empty value suppresses the default. Native course backup/restore preserves the value, text mode and alignment.

Use the separate **Certificate Multitool – Cert. Element** certificate element to control font, size, colour, width, position and left/centre/right alignment and line height in Certificate manager. This field does not require Certificate manager. Plain text is the default and displays HTML tags literally. Course settings offer **Text mode → Limited HTML** for each course value, entered as source without an editor. Allowed tags are `strong`, `b`, `em`, `i`, `u`, `small`, `ul`, `ol`, `li`, `br` and `p`. All attributes, CSS and media are removed; executable content is discarded. Use HTML `br` or `p` for breaks in this mode. Long lines can wrap within the certificate element width.

Changing Text mode keeps the raw source. New certificate issues save the selected mode with the content; existing issues keep their saved mode.

Choose **Text alignment** separately for each course field: **Use template alignment** (default), **Left**, **Centre** or **Right**, for either text mode. An explicit choice aligns text inside the block positioned by the template. Set the block width and position, font and line height in the template once; the same template can serve different course alignments. List items retain their left-aligned hanging indents. New issues save the chosen mode and alignment with the text.

On upgrade, existing saved values retain their previous Plain text or Limited HTML interpretation and inherit template alignment. Existing issues keep their saved presentation. Defaults from earlier HTML-enabled fields keep their interpretation until a course saves its own choice.

**Vertical alignment** follows the same default/override model: choose **Inherit from template**, **Top**, **Middle** or **Bottom** for each field in course settings; the certificate element supplies a **Top**, **Middle** or **Bottom** default. Optionally set **Area height (mm)** in the element. Position Y is then the upper edge of the area: Top aligns text there, Bottom aligns it to Position Y plus the height. A one-line value and a multiline value can use the same reserved space. The PDF renderer rejects text that does not fit rather than clipping it; enlarge the area or adjust text, width, font size or line height.

Leave Area height empty to preserve legacy anchoring: Top starts at Position Y and Bottom ends at Position Y. Existing elements have no area height, so their positions remain unchanged. Issued exports retain explicit course choices; inherited choices and layout follow the current template when PDFs are regenerated. Existing stored PDFs remain unchanged.

For certificates, **Middle** requires Certificate Multitool – Cert. Element 0.7.0-beta or later. Choose **Middle** for vertical centring within a fixed-height text area. The whole block is centred, so one-line and two-line titles share the same vertical midpoint. Set an Area height in the certificate element first; a missing height is reported instead of silently changing the alignment. Course choices override the element default in both text modes. Existing values and issued exports keep their previous choices.

## Smaller extra text

In course settings, choose **Limited HTML** and save text such as `<small>Advanced Training</small><br>Certificate`, or `Certificate<br><small>Advanced Training</small>`. **Small text size (%)** in the certificate element controls its size from 10 to 100 percent of the base font size (default 60%). At 45 pt, a 60% extra line is 27 pt. The same element can place the extra line above or below the main line; the entire block still uses the configured area height and vertical alignment. Nested small tags do not shrink further. User attributes and CSS remain removed. Requires Certificate Multitool - Cert. Element 0.8.0-beta and Custom Field 0.7.0-beta or later.

Existing saved Plain text and Limited HTML values keep their interpretation on upgrade, including old HTML that happened to contain small tags. Save a course value again explicitly in Limited HTML mode to enable small. Partial updates without a submitted text mode preserve the previous interpretation. New issues retain the selected HTML semantics; existing issues retain their saved interpretation. The percentage is a template layout setting used again when a PDF is regenerated.


## Guides

- [Course editor guide](docs/COURSE_EDITOR_GUIDE.md) ([PDF](docs/COURSE_EDITOR_GUIDE.pdf))
- [Administrator guide](docs/ADMINISTRATOR_GUIDE.md) ([PDF](docs/ADMINISTRATOR_GUIDE.pdf))

## Licence

GNU GPL v3 or later. See [LICENSE.md](LICENSE.md). No product activation is required.
