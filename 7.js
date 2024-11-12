document.addEventListener('DOMContentLoaded', () => {
    const sliderTrack = document.querySelector('.slider-track');
    const images = document.querySelectorAll('.slider img');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const currentPage = document.getElementById('currentPage');
    const totalPages = document.getElementById('totalPages');

    let currentIndex = 0;
    const imagesPerPage = window.innerWidth > 768 ? 3 : 1;
    const totalPagesCount = Math.ceil(images.length / imagesPerPage);

    totalPages.textContent = totalPagesCount;

    function updateSliderPosition() {
        const offset = -currentIndex * (100 / imagesPerPage);
        sliderTrack.style.transform = `translateX(${offset}%)`;
        currentPage.textContent = currentIndex + 1;
    }

    function showNext() {
        if (currentIndex < totalPagesCount - 1) {
            currentIndex++;
            updateSliderPosition();
        }
    }

    function showPrev() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSliderPosition();
        }
    }

    nextButton.addEventListener('click', showNext);
    prevButton.addEventListener('click', showPrev);

    window.addEventListener('resize', () => {
        const newImagesPerPage = window.innerWidth > 768 ? 3 : 1;
        if (newImagesPerPage !== imagesPerPage) {
            currentIndex = 0;
            updateSliderPosition();
        }
    });
    
    updateSliderPosition();
});
