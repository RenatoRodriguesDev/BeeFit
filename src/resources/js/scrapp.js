import fs from 'fs';
import axios from 'axios';
import path from 'path';

// dirname (ESM)
const __dirname = path.dirname(new URL(import.meta.url).pathname);

// caminhos dentro do container
const imageFolder = path.resolve(__dirname, '../../public/images/exercises');
const videoFolder = path.resolve(__dirname, '../../public/videos/exercises');

// garantir pasta
if (!fs.existsSync(videoFolder)) {
    fs.mkdirSync(videoFolder, { recursive: true });
}

// gerar várias hipóteses de nome de vídeo
const generateVideoNames = (imageName) => {
    let base = imageName
        .replace(/_thumbnail_?@?\dx?/i, '')
        .replace('.jpg', '');

    return [
        `${base}.mp4`,
        `${base}_.mp4`,
        `${base.replace('_FIX', '')}.mp4`,
        `${base.replace('_FIX', '')}_.mp4`,
    ];
};

// download (sem HEAD, tenta direto)
const downloadVideo = async (videoUrl, savePath) => {
    try {
        const response = await axios.get(videoUrl, { responseType: 'stream' });

        await new Promise((resolve, reject) => {
            const writer = fs.createWriteStream(savePath);
            response.data.pipe(writer);

            writer.on('finish', resolve);
            writer.on('error', reject);
        });

        console.log(`✅ ${path.basename(savePath)} baixado`);
        return true;
    } catch (err) {
        return false; // falhou, tenta próxima variação
    }
};

// MAIN
const run = async () => {
    console.log('📂 Imagens:', imageFolder);
    console.log('📂 Vídeos:', videoFolder);

    let files;

    try {
        files = fs.readdirSync(imageFolder);
    } catch (err) {
        console.error('❌ Erro ao ler pasta:', err);
        return;
    }

    for (const imageName of files) {
        if (!imageName.endsWith('.jpg')) continue;

        const possibleNames = generateVideoNames(imageName);

        let success = false;

        for (const videoName of possibleNames) {
            const videoUrl = `https://d2l9nsnmtah87f.cloudfront.net/exercise-assets/${videoName}`;
            const videoPath = path.join(videoFolder, videoName);

            // evitar downloads repetidos
            if (fs.existsSync(videoPath)) {
                console.log(`⏭️ Já existe: ${videoName}`);
                success = true;
                break;
            }

            const ok = await downloadVideo(videoUrl, videoPath);

            if (ok) {
                success = true;
                break;
            }
        }

        if (!success) {
            console.log(`❌ Falhou todas variações: ${imageName}`);
        }
    }

    console.log('🏁 Terminado');
};

run();