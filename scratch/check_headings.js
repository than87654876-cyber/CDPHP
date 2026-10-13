const fs = require('fs');
const content = fs.readFileSync('scratch/build_comprehensive_report.cjs', 'utf8');
const regex = /pHeading[1234]\("([^"]+)"\)/g;
let match;
while ((match = regex.exec(content)) !== null) {
    console.log(match[1]);
}
