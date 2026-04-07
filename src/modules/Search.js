import $ from "jquery";
class Search {
  // 1. describe and create/initiate our object
  constructor() {
    this.resultsDiv = document.querySelector("#search-results");
    this.searchField = document.querySelector("#search-term");
    this.searchField.value = "";
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
    this.currentPageModul = scData.currentModul;
    this.rootUrl = scData.root_url;
    this.events();
  }

  // 2. events
  events() {
    this.searchField.addEventListener("keyup", this.typingLogic.bind(this));

    // Hide search results when clicking outside
    document.addEventListener("click", this.handleOutsideClick.bind(this));
  }

  // 3. methods (function, action...)
  typingLogic() {
    if (this.searchField.value != this.previousValue) {
      clearTimeout(this.typingTimer);
      if (this.searchField.value) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>';
          this.isSpinnerVisible = true;
          // Update ARIA attributes for accessibility
          this.searchField.setAttribute("aria-expanded", "true");
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 500);
      } else {
        this.resultsDiv.innerHTML = "";
        this.isSpinnerVisible = false;
        // Update ARIA attributes for accessibility
        this.searchField.setAttribute("aria-expanded", "false");
      }
    }
    this.previousValue = this.searchField.value;
    console.log(this.searchField.value);
  }

  handleOutsideClick(e) {
    // Check if the click is outside both search field and results div
    if (
      !this.searchField.contains(e.target) &&
      !this.resultsDiv.contains(e.target) &&
      this.resultsDiv.innerHTML !== ""
    ) {
      // Hide search results using the same method as in typingLogic
      this.resultsDiv.innerHTML = "";
      this.isSpinnerVisible = false;
      // Update ARIA attributes for accessibility
      this.searchField.setAttribute("aria-expanded", "false");
    }
  }

  getResults() {
    // jQuery.ajax({
    //   type: "GET",
    //   url: this.rootUrl + '/wp-json/PE_supportcenter/posts?term=' + this.searchField.value,
    //   data: '',
    //   datatype: "html",
    //   success: (results)=> {

    //     if(!results.length){
    //       this.resultsDiv.innerHTML =`
    //        <div>Kein Ergebnis</div>`;
    //     }
    //     else{
    //       this.resultsDiv.innerHTML =
    //         `
    //         ${results.map(
    //           item => `
    //             <div><a class="scrollLink"
    //             href="${this.currentPageModul==item.modul.slug ? '': `${this.rootUrl}/teamproq-supportcenter/${item.modul.slug}`}#${item.slug}">${item.title} - ${item.modul.name}
    //             </a></div>`
    //           ).join("")}
    //         `;
    //       this.isSpinnerVisible = false;
    //     }
    //   }
    // });
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        const results = JSON.parse(xhr.responseText);
        if (!results.length) {
          this.resultsDiv.innerHTML =
            ' <div class="search-result-item"><div>Kein Ergebnis</div></div>';
        } else {
          this.resultsDiv.innerHTML = results
            .map(
              (item) => `
            <div class="search-result-item">
              <a class="${
                this.currentPageModul == item.modul.slug
                  ? "anchorToThisPage"
                  : "anchorToNewPage"
              }" 
                href="${
                  this.currentPageModul == item.modul.slug
                    ? ""
                    : `${this.rootUrl}/teamproq-supportcenter/${item.modul.slug}`
                }#${item.slug}">
                ${item.title} - ${item.modul.name}
              </a>
            </div>
          `,
            )
            .join("");
          this.isSpinnerVisible = false;
        }
      }
    };
    xhr.open(
      "GET",
      `${this.rootUrl}/wp-json/PE_supportcenter/posts?term=${this.searchField.value}`,
      true,
    );
    xhr.send();
  }
}

export default Search;
