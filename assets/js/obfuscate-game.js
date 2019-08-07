let JavaScriptObfuscator = require('javascript-obfuscator');

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

let fileSystem = require('fs'),
    gameJs = fileSystem.readFileSync('game.js', "utf8");

let obfuscationResult = JavaScriptObfuscator.obfuscate(gameJs, config);

fileSystem.writeFile('game2.min.js', obfuscationResult, err => 
{
  if (err) throw err;
  console.log('game2.min.js created');
});
