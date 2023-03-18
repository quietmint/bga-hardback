#!/bin/bash -eu

# Clean
rm -vf modules/*.js* hardback.css

# Build app
cd src
npx vite build --mode production

# Move JavaScript
perl -0777 -p -i'*' -e 's~\s*-NO-BREAK-\s*~~g' dist/index.js
mv dist/*.js* ../modules/

# Move and rewrite CSS
mv dist/index.css ../temp.css
echo -e "\n/*bga.css*/" >> ../temp.css
cat bga.css >> ../temp.css
perl -pe 's~url[(]([a-zA-Z0-9\-_\.]+?)[)]~url(img/\1)~g' ../temp.css > ../hardback.css
rm ../temp.css
rm -r dist

# Upload to BGA
cd ..
bga hardback
