CHILO EXOTICS — ELECTRIC AGE ENTRY

Your website now opens with the supplied animated electric-border design and
asks: “Are you 18 or older?”

YES, I'M 18 OR OLDER
Opens the existing Chilo homepage. The answer is remembered for the tab's
session, so visitors can refresh without being asked again.

NO, I'M UNDER 18
Keeps the homepage hidden and shows an access-restricted screen with a
Leave website button. Refreshing retains the restriction for that session.

UPLOAD TO YOUR EXISTING REPOSITORY
1. Extract this ZIP.
2. Open https://github.com/cannapharm76-create/chiloworld
3. Create a review branch from main, for example electric-age-entry.
4. Choose Add file > Upload files and upload index.html into the top-level
   folder, replacing the existing index.html in that branch.
5. Commit the upload to your review branch.

Only index.html changed. Keep your existing styles.css and script.js.
Copies of both are included so the ZIP also works as a complete local preview.
No React setup, npm installation, API key, or build command is required.

Do not merge to a branch that automatically deploys until you are ready for
the page to go live. This delivery has not changed GitHub or published the site.

PREVIEW
Extract the ZIP and open index.html in your browser. For another fresh test
after choosing an answer, close the test tab and open the file in a new tab.

DETAILS
- The original homepage, stylesheet, contact form, and navigation are kept.
- Keyboard users can choose either option, with focus moved to the next view.
- The animation respects reduced-motion preferences and has a Motion toggle.
- Animation stops after entry and pauses while the browser tab is hidden.
- Incoming section links, such as /#contact, still reach the intended section
  after confirmation.
- A static border appears if Canvas is unavailable. If browser storage is
  blocked, the buttons still work, but the answer cannot survive a refresh.
- With JavaScript disabled, the homepage stays hidden and the entry page
  explains that JavaScript is required.

AGE SETTING
The entry uses 18 as requested. Search index.html for:
  const MINIMUM_AGE = 18;
This setting updates all age labels and the remembered-answer key.

This is a browser-side age declaration, not identity verification or a login.
It does not collect a birth date or send the choice to a server.

CREDITS
Electric-border animation adapted from the ElectricBorder component supplied
in Pasted text(4).txt. The supplied component credits @BalintFerenczy:
https://codepen.io/BalintFerenczy/pen/KwdoyEN

Based on repository commit da63128cd1ced9ddaaab9a5fdcd06f3fe06bd48f,
retrieved October 2, 2026.
