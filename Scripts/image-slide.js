let currentImageIndex = 0;

function changeImage(direction) {
  const images = document.querySelectorAll('.product-image-img');
  const leftButton = document.querySelector('.left-btn');
  const rightButton = document.querySelector('.right-btn');
  
  if (direction === 'right') {
    if (currentImageIndex < images.length - 1) {
      images[currentImageIndex].style.display = 'none';
      currentImageIndex++;
      images[currentImageIndex].style.display = 'block';

      leftButton.style.display = 'block';
      rightButton.style.display = 'block';
    }

    if (currentImageIndex === images.length - 1) {
      rightButton.style.display = 'none';
    }
  }

  if (direction === 'left') {
    if (currentImageIndex > 0) {
      images[currentImageIndex].style.display = 'none';
      currentImageIndex--;
      images[currentImageIndex].style.display = 'block';

      rightButton.style.display = 'block';
    }

    if (currentImageIndex === 0) {
      leftButton.style.display = 'none';
    }
  }
}
