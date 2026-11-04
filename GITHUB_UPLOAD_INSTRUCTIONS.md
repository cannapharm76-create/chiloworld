# Upload this website to `cannapharm76-create/chiloworld`

The connected GitHub integration was able to read the repository but returned **403 Resource not accessible by integration** for both branch creation and file creation. No files were written automatically.

## Safest path: separate preview branch

1. Download the supplied website ZIP and extract it to a local folder.
2. Open GitHub Desktop and clone `https://github.com/cannapharm76-create/chiloworld`.
3. In GitHub Desktop choose **Current Branch → New Branch** and name it `chilo-editorial-preview`.
4. Within the cloned repository create a new directory named `design-preview`.
5. Copy the **contents** of the extracted ZIP into that folder. The result should contain `design-preview/index.html`, `design-preview/catalog.html`, `design-preview/assets/styles.css` and the other supplied files.
6. In GitHub Desktop commit as `Add Chilo Exotics editorial website preview` and choose **Publish branch**.
7. Open the branch on GitHub to inspect the files. The existing `main/index.html` remains untouched. Do not merge or deploy publicly until legal review and batch-photo verification.

## Equivalent Git terminal commands

```bash
git clone https://github.com/cannapharm76-create/chiloworld.git
cd chiloworld
git switch -c chilo-editorial-preview
mkdir -p design-preview
# Copy every extracted file and subfolder into design-preview/.
git add design-preview
git commit -m "Add Chilo Exotics editorial website preview"
git push -u origin chilo-editorial-preview
```

All website asset paths are relative, so the site works within the `design-preview/` folder. The Netlify config in this folder only applies if you intentionally deploy that folder as the site's publish root.

## Critical notes

- This is a **design and functional prototype**, not a public sales site.
- The catalog uses **illustrative Unsplash photography**, never actual named Chilo batch photos.
- Strain records are historical names and **not current stock**; there are no prices, purchases or online orders.
- A public catalog of cannabis flower in Thailand requires specialist legal review, and any restricted trade catalog needs real server-side access control.
