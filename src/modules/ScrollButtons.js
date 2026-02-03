// Scroll-to-top functionality for supportcenter modules

let btnScrollToTop = document.querySelector(".scroll-buttons");

let waitForTarget = function (target, callback) {
  // If the target is not recognized, (html is not ready)
  if (target === null) {
    // then wait 100ms
    setTimeout(function () {
      // try again
      let target = document.querySelector(".scroll-buttons");
      waitForTarget(target, callback);
    }, 100);
    // if the target is there
  } else {
    // run the function
    callback();
  }
};

var showBtn = () => {
  window.addEventListener("scroll", (e) => {
    const scrollButtons = document.querySelector(".scroll-buttons");
    if (scrollButtons) {
      scrollButtons.style.display = window.scrollY > 20 ? "block" : "none";
    }
  });

  const scrollButtons = document.querySelector(".scroll-buttons");
  if (scrollButtons) {
    scrollButtons.addEventListener("click", scrollToTop);
  }
};

function scrollToTop() {
  document.documentElement.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

// Initialize when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    waitForTarget(btnScrollToTop, showBtn);
  });
} else {
  waitForTarget(btnScrollToTop, showBtn);
}
