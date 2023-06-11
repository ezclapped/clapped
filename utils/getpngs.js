document.addEventListener('DOMContentLoaded', function() {
    fetch('utils/getpngs.php')
        .then(response => response.json())
        .then(data => {
            const fileCount = data;
            const fileCountElement = document.getElementById('fileCount');
            fileCountElement.textContent = fileCount;
        })
        .catch(error => console.log(error));
})