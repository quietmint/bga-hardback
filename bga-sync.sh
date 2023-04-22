#!/bin/bash -eu

# Clean
rm -vf modules/*.js* hardback.css

# Build app
cd src
npx vite build --mode production

# Move JavaScript
perl -0777 -p -i'*' -e 's~\s*-NO-BREAK-\s*~~g' dist/index.js
mv dist/*.js* ../modules/

# Move CSS
mv dist/index.css ../hardback.css
rm -r dist

# Upload to BGA
cd ..
bga hardback
