document.querySelectorAll('input[type="file"][id^="image_file_"]').forEach(fileInput => {
  fileInput.addEventListener('change', () => {
    const imageItem = fileInput.closest('.image-item');
    if (!imageItem) return;

    const imageId = imageItem.dataset.imageId;
    if (!imageId) return;

    if (fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        const imgPreview = document.getElementById(`img-preview-${imageId}`);
        if (imgPreview) {
          imgPreview.src = e.target.result;
        }
      };
      reader.readAsDataURL(fileInput.files[0]);
    }
  });
});

