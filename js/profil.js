document.getElementById('profilePicture').addEventListener('mouseover', function() {
    document.querySelector('.overlay').style.opacity = 1;
});

document.getElementById('profilePicture').addEventListener('mouseout', function() {
    document.querySelector('.overlay').style.opacity = 0;
});

document.getElementById('profilePicture').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function() {
    document.getElementById('submitButton').style.display = 'block';
    document.getElementById('error-message').style.display = 'none';
});

document.getElementById('profileForm').addEventListener('submit', function(event) {
    var fileInput = document.getElementById('fileInput');
    if (!fileInput.value) {
        event.preventDefault();
        document.getElementById('error-message').style.display = 'block';
    }
});
