const path = require('path');

const presets = [
    "@babel/preset-react",
    [
        "@babel/preset-env",
        {
            modules: false,
            targets: {
                edge: "17",
                firefox: "60",
                chrome: "67",
                safari: "11.1",
            }
        }
    ]
];

const plugins = [
    [
        "@babel/plugin-proposal-decorators",
        {
            legacy: true
        }
    ],
    "@babel/plugin-proposal-class-properties",
    "@babel/plugin-transform-runtime",
    "@babel/plugin-transform-logical-assignment-operators",
    "@babel/plugin-transform-nullish-coalescing-operator",
    [
        "module-resolver",
        {
            root: [
                "./src"
            ],
            alias: {
                "@images": "./assets/images"
            }
        }
    ]
];

module.exports = { presets, plugins };
