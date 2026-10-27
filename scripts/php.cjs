const { existsSync } = require("node:fs");
const { resolve } = require("node:path");
const { spawn } = require("node:child_process");
const local = resolve(".tools/php/php.exe");
const binary = process.env.PHP_BINARY || (existsSync(local) ? local : "php");
const child = spawn(binary, process.argv.slice(2), {
    stdio: "inherit",
    windowsHide: true,
});
child.on("exit", (code) => process.exit(code ?? 1));
child.on("error", (error) => {
    console.error(error.message);
    process.exit(1);
});
