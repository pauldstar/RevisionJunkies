let fileSystem = require('fs');

let gameJsText = fileSystem.readFileSync(
  '/home/pvhhqumha6t1/public_html/assets/js/game.js',
  'utf8'
);

let obfuscator = require('javascript-obfuscator');

let config = {
  "compact": true,
  "controlFlowFlattening": false,
  "controlFlowFlatteningThreshold": 0.75,
  "deadCodeInjection": false,
  "deadCodeInjectionThreshold": 0.4,
  "debugProtection": false,
  "debugProtectionInterval": false,
  "disableConsoleOutput": false,
  "domainLock": [],
  "identifierNamesGenerator": "mangled",
  "identifiersPrefix": "",
  "inputFileName": "game.js",
  "log": false,
  "renameGlobals": true,
  "reservedNames": [],
  "reservedStrings": [],
  "seed": 0,
  "selfDefending": false,
  "sourceMap": false,
  "sourceMapBaseUrl": "",
  "sourceMapFileName": "",
  "sourceMapMode": "separate",
  "stringArray": true,
  "rotateStringArray": false,
  "stringArrayEncoding": true,
  "stringArrayThreshold": 1,
  "target": "browser",
  "transformObjectKeys": true,
  "unicodeEscapeSequence": false
};

let obfuscationResult = obfuscator.obfuscate(gameJsText, config);

let writeFileCallBack = err =>
{
  if (err) throw err;
  console.log('Obfuscation success: game.min.js created');
}

fileSystem.writeFile(
  '/home/pvhhqumha6t1/public_html/assets/js/game.min.js',
  obfuscationResult,
  writeFileCallBack
);
