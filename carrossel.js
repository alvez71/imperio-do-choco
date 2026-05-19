document.addEventListener("DOMContentLoaded", () => {

    const slides = document.querySelectorAll(".novo-carrossel .slide");

    const nextBtn = document.querySelector(".novo-carrossel .next");

    const prevBtn = document.querySelector(".novo-carrossel .prev");

    let index = 0;

    function updateCarousel() {

        slides.forEach(slide => {

            slide.classList.remove(
                "active",
                "left",
                "right",
                "hidden"
            );

        });

        slides[index].classList.add("active");

        let leftIndex =
            (index - 1 + slides.length) % slides.length;

        let rightIndex =
            (index + 1) % slides.length;

        slides[leftIndex].classList.add("left");

        slides[rightIndex].classList.add("right");

        slides.forEach((slide, i) => {

            if (
                i !== index &&
                i !== leftIndex &&
                i !== rightIndex
            ) {

                slide.classList.add("hidden");

            }

        });

    }

    nextBtn.addEventListener("click", () => {

        index++;

        if (index >= slides.length) {

            index = 0;

        }

        updateCarousel();

    });

    prevBtn.addEventListener("click", () => {

        index--;

        if (index < 0) {

            index = slides.length - 1;

        }

        updateCarousel();

    });

    setInterval(() => {

        index++;

        if (index >= slides.length) {

            index = 0;

        }

        updateCarousel();

    }, 4000);

    updateCarousel();

});