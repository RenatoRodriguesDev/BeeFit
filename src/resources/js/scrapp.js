import fs from 'fs';
import axios from 'axios';
import path from 'path';

// Obtém o diretório atual com import.meta.url
const __dirname = path.dirname(new URL(import.meta.url).pathname);

// Caminho absoluto para o diretório src
const srcDir = path.resolve(__dirname, '../../..');

// Caminho onde estão as imagens (ajuste conforme necessário)
const imageFolder = path.join(srcDir, 'src/public/images/exercises');  // Caminho correto relativo à pasta src
const videoFolder = path.join(srcDir, 'src/public/videos/exercises');

// Cria a pasta para os vídeos, se não existir
if (!fs.existsSync(videoFolder)) {
    fs.mkdirSync(videoFolder);
}

// Função para baixar o vídeo
const downloadVideo = async (videoUrl, savePath) => {
    try {
        const response = await axios.get(videoUrl, { responseType: 'stream' });
        const writer = fs.createWriteStream(savePath);
        response.data.pipe(writer);

        writer.on('finish', () => {
            console.log(`${savePath} baixado com sucesso.`);
        });

        writer.on('error', (err) => {
            console.error('Erro ao salvar o arquivo', err);
        });
    } catch (err) {
        console.error('Erro ao fazer o download do vídeo:', err);
    }
};

// Lista todos os arquivos da pasta de imagens
fs.readdir(imageFolder, (err, files) => {
    if (err) {
        console.error('Erro ao ler a pasta de imagens:', err);
        return;
    }

    files.forEach((imageName) => {
        // Verifica se o arquivo é uma imagem com o sufixo '_thumbnail@3x'
        if (imageName.endsWith('_thumbnail@3x.jpg')) {
            // Substitui o sufixo da imagem para gerar o link do vídeo
            const videoName = imageName.replace('_thumbnail@3x.jpg', '.mp4');
            const videoUrl = `https://d2l9nsnmtah87f.cloudfront.net/exercise-assets/${videoName}`;

            // Caminho para salvar o vídeo
            const videoPath = path.join(videoFolder, videoName);

            // Baixa o vídeo
            downloadVideo(videoUrl, videoPath);
        }
    });
});