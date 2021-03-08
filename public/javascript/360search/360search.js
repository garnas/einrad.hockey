window.ss360Config = {
    allowCookies: false,
    suggestions: {
        showImages: false
    },
    style: {
        themeColor: "#FFFFFF",
        accentColor: "#6C7FBE",
        searchBox: {
            text: {
                color: "#333333",
                size: "13px"
            },
            background: {
                color: "#FFFFFF"
            },
            border: {
                color: "#ababab",
                radius: "0px"
            },
            padding: "8px",
            button: {
                text: " ",
                icon: "magnifier",
                color: "#FFFFFF",
                iconPadding: "8px",
                backgroundColor: "#6C7FBE"
            }
        },
        loaderType: "circle",
        animationSpeed: 500,
        additionalCss: ""
    },
    searchBox: {
        selector: ".searchbox",
        placeholder: "Suche",
        searchButton: ".searchbutton"
    },
    results: {
        group: false
    },
    tracking: {
        providers: []
    },
    layout: {
        mobile: {
            showImages: false,
            showUrl: true
        },
        desktop: {
            showImages: false,
            showUrl: true
        }
    },
    language: "de",
    siteId: "test.einrad.hockey",
    showErrors: false
};

var e=document.createElement("script");
e.async=!0;
e.src="https://cdn.sitesearch360.com/v13/sitesearch360-v13.min.js";
document.getElementsByTagName("body")[0].appendChild(e);