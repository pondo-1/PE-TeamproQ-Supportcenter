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
  }

  // 3. methods (function, action...)
  typingLogic() {
    if (this.searchField.value != this.previousValue) {
      clearTimeout(this.typingTimer);
      if (this.searchField.value) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>';
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 500);
      } else {
        this.resultsDiv.innerHTML = "";
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.value;
    console.log(this.searchField.value);
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
    //             href="${this.currentPageModul==item.modul.slug ? '': `${this.rootUrl}/supportcenter/${item.modul.slug}`}#${item.slug}">${item.title} - ${item.modul.name}
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
          this.resultsDiv.innerHTML = "<div>Kein Ergebnis</div>";
        } else {
          this.resultsDiv.innerHTML = results
            .map(
              (item) => `
            <div>
              <a class="${
                this.currentPageModul == item.modul.slug
                  ? "anchorToThisPage"
                  : "anchorToNewPage"
              }" 
                href="${
                  this.currentPageModul == item.modul.slug
                    ? ""
                    : `${this.rootUrl}/supportcenter/${item.modul.slug}`
                }#${item.slug}">
                ${item.title} - ${item.modul.name}
              </a>
            </div>
          `
            )
            .join("");
          this.isSpinnerVisible = false;
        }
      }
    };
    xhr.open(
      "GET",
      `${this.rootUrl}/wp-json/PE_supportcenter/posts?term=${this.searchField.value}`,
      true
    );
    xhr.send();
  }

}

export default Search;
