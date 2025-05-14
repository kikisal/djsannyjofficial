const https = require("https");
const http = require("http");
const fs = require("fs");
const path = require("path");


const out_path = "./assets/djsannyj/images/";

const urls = [
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-dark.png",
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-dark.png",
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-light@2x.png",
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-dark.png",
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-dark.png",
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-light@2x.png",
    "https://www.davidguetta.com/wp-content/uploads/2016/09/logo-dark.png",
    "https://davidguetta.com/wp-content/uploads/2022/09/ext-1.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/09/ext.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/05/ext.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/04/ext.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/03/ext-4.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/03/ext-3.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/03/ext-2.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/03/ext-1.jpg",
    "https://davidguetta.com/wp-content/uploads/2022/03/ext.jpg",
];

function download(url, dest) {
    return new Promise((resolve, reject) => {
        const protocol = url.startsWith('https') ? https : http;
        protocol.get(url, (res) => {
            if (res.statusCode !== 200) {
                return reject(new Error(`Failed to get '${url}' (${res.statusCode})`));
            }
            const fileStream = fs.createWriteStream(dest);
            res.pipe(fileStream);
            fileStream.on("finish", () => {
                fileStream.close(resolve);
            });
        }).on("error", (err) => {
            fs.unlink(dest, () => {}); // Delete the file async if error
            reject(err);
        });
    });
}

async function downloadAll(urls) {
    for (let i = 0; i < urls.length; i++) {
        const url = urls[i];
        const ext = path.extname(new URL(url).pathname).split("?")[0] || ".jpg";
        const filename = `img_${i + 1}${ext}`;
        const filepath = path.join(out_path, filename);
        try {
            console.log(`Downloading ${url} → ${filename}`);
            await download(url, filepath);
            console.log(`Saved to ${filename}`);
        } catch (err) {
            console.error(`Error downloading ${url}:`, err.message);
        }
    }
}

downloadAll(urls);