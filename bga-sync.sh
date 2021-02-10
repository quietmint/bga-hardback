#!/bin/bash -eu

# Build app
cd src
npx vite build

# Move JavaScript
mv build/index.js ../modules/HardbackVue.js
mv build/index.js.map ../modules/HardbackVue.js.map

# Move and rewrite CSS
mv build/style.css ../temp.css
echo -e "\n/*bga.css*/" >> ../temp.css
cat bga.css >> ../temp.css
rm build/*.css
perl -pe 's~url[(]([a-zA-Z0-9\-_]+?\.(gif|png|jpg|woff|woff2))[)]~url(img/\1)~g' ../temp.css > ../hardback.css
rm ../temp.css

# Move images
mv build/* ../img/
rmdir build

# Upload to BGA
cd ..
bga hardback
