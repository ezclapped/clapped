document.addEventListener('DOMContentLoaded', function() {
    const folderSizeElement = document.getElementById('folder-size');

    fetch('utils/server.php')
        .then(response => response.json())
        .then(data => {
            const folderSize = data.folderSize;
            folderSizeElement.textContent = `${formatBytes(folderSize)}`;
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

function formatBytes(bytes) {
    if (bytes === 0) {
        return '0 Bytes';
    }

    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    const formattedSize = (bytes / Math.pow(1024, i)).toFixed(2);

    return `${formattedSize} ${sizes[i]}`;
}